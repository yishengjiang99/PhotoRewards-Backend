// Example# 5.1 .. simple *semaphore* example (main.c)
//   main.c

#include <linux/init.h>
#include <linux/module.h>
#include <linux/kernel.h>
#include <linux/version.h>
#include <linux/proc_fs.h>
#include <asm/uaccess.h>

#include <linux/sched.h>

#include <linux/semaphore.h>

#if LINUX_VERSION_CODE > KERNEL_VERSION(2,6,36)
#include <generated/utsrelease.h>
#define init_MUTEX(a) sema_init(a,1)
#else
#include <linux/utsrelease.h>
#endif

MODULE_LICENSE("GPL");

#define PROC_HELLO_LEN 8
#define HELLO "hello"
#define MYDEV "MYDEV"

struct proc_hello_data {
	char proc_hello_name[PROC_HELLO_LEN + 1];
	char proc_hello_value[132];
	char proc_hello_flag;
	
	struct semaphore *proc_hello_sem;
};

static struct proc_hello_data hello_data;

static struct proc_dir_entry *proc_hello;
static struct proc_dir_entry *proc_mydev;

static int read_hello (char *buf, char **start, off_t offset, 
		int len, int *eof, void *unused) 
{
	struct proc_hello_data *usrsp=&hello_data;

	*eof = 1;

	if (offset) { return 0; }
	else if (usrsp->proc_hello_flag) {
		usrsp->proc_hello_flag=0;
		return(sprintf(buf,
				"Hello .. I got \"%s\"\n", 
				usrsp->proc_hello_value)); 
	}
	else
		return(sprintf(buf,
				"Hello from process %d\n", 
				(int)current->pid));
}

static int write_hello (struct file *file,const char * buf, 
		unsigned long count, void *data) 
{
	int length=count;
	int err=0;
	struct proc_hello_data *usrsp=&hello_data;

				length = (length<PROC_HELLO_LEN)? length:PROC_HELLO_LEN;

        // semaphore decr
        if (down_interruptible(hello_data.proc_hello_sem))
            return -ERESTARTSYS;
				// Begin Critical section
        printk(KERN_ALERT "2470:5.1: got semaphore!\n");

				err=copy_from_user(usrsp->proc_hello_value, buf, length); 

        // semaphore incr
        up(hello_data.proc_hello_sem);
				
				// End Critical section
        printk(KERN_ALERT "2470:5.1: released semaphore!\n");

	// check for copy_from_user error here (immediately upon sem release)
	if (err) 	{
        	printk(KERN_ALERT "2470:5.1: return fault!\n");
		return -EFAULT;
	}

	usrsp->proc_hello_value[length-1]=0;
	usrsp->proc_hello_flag=1;
	return(length);
}

static int my_init (void) {
	proc_mydev = proc_mkdir(MYDEV,0);

	proc_hello = create_proc_entry(HELLO,0,proc_mydev);
	proc_hello->read_proc = read_hello;
	proc_hello->write_proc = write_hello;

#if LINUX_VERSION_CODE <= KERNEL_VERSION(2,6,29)
	proc_hello->owner = THIS_MODULE;
#endif

	hello_data.proc_hello_sem=(struct semaphore *) 
		kmalloc(sizeof(struct semaphore),GFP_KERNEL);

	// from <linux/semaphore.h>
	// #define init_MUTEX(sem)         sema_init(sem, 1)
	// #define init_MUTEX_LOCKED(sem)  sema_init(sem, 0)
	init_MUTEX(hello_data.proc_hello_sem);

	hello_data.proc_hello_flag=0;

        // module init message
        printk(KERN_ALERT "2470:5.1: main initialized!\n");
	return 0;
}

static void my_exit (void) {

	kfree(hello_data.proc_hello_sem);

	if (proc_hello)
		remove_proc_entry (HELLO, proc_mydev);
	if (proc_mydev)
		remove_proc_entry (MYDEV, 0);

        // module exit message
        printk(KERN_ALERT "2470:5.1: main destroyed!\n");
}

module_init (my_init);
module_exit (my_exit);
