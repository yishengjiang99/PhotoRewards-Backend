// Example# 5.3b .. simple *rw spinlock* example (main.c)   ... oldstyle (note lines 18-21 + lines 114-115
//   main.c

#include <linux/init.h>
#include <linux/module.h>
#include <linux/kernel.h>
#include <linux/version.h>

#if LINUX_VERSION_CODE > KERNEL_VERSION(2,6,36)
#include <generated/utsrelease.h>
#else
#include <linux/utsrelease.h>
#endif

#include <linux/proc_fs.h>
#include <asm/uaccess.h>

#if LINUX_VERSION_CODE >= KERNEL_VERSION(2,6,39)
#define SPIN_LOCK_UNLOCKED __SPIN_LOCK_UNLOCKED(old_style_spin_init)
#define RW_LOCK_UNLOCKED __RW_LOCK_UNLOCKED(old_style_rw_init)
#endif

#include <linux/spinlock.h>

#include <linux/sched.h>

MODULE_LICENSE("GPL");

#define PROC_HELLO_LEN 8
#define HELLO "hello2"
#define MYDEV "MYDEV"

struct proc_hello_data {
	char proc_hello_name[PROC_HELLO_LEN + 1];
	char proc_hello_value[132];
	char proc_hello_flag;
	
	spinlock_t proc_hello_sp;
	rwlock_t proc_hello_rw;
};

static struct proc_hello_data hello_data;

static struct proc_dir_entry *proc_hello;
static struct proc_dir_entry *proc_mydev;

static int read_hello (char *buf, char **start, off_t offset, 
		int len, int *eof, void *unused) 
{
	struct proc_hello_data *usrsp=&hello_data;
	int n=0;

	*eof = 1;

	read_trylock(&hello_data.proc_hello_rw);
       	printk(KERN_ALERT "2470:5.3a: got read lock!\n");

	if (offset) { n=0; }
	else if (usrsp->proc_hello_flag) {
		usrsp->proc_hello_flag=0;
		n=sprintf(buf, "Hello .. I got \"%s\"\n", 
				usrsp->proc_hello_value); 
	}
	else
		n=sprintf(buf, "Hello from process %d\n", 
				(int)current->pid);

	read_unlock(&hello_data.proc_hello_rw);
       	printk(KERN_ALERT "2470:5.3a: released read lock!\n");

	return n;
}

static int write_hello (struct file *file,const char * buf, 
		unsigned long count, void *data) 
{
	int length=count;
	int err=0;
	struct proc_hello_data *usrsp=&hello_data;

	length = (length<PROC_HELLO_LEN)? length:PROC_HELLO_LEN;

        // spinlock get - returns nonzero on success. zero otherwise.
	if (write_trylock(&hello_data.proc_hello_rw)) {
        	printk(KERN_ALERT "2470:5.3a: got write lock!\n");
		err=copy_from_user(usrsp->proc_hello_value, buf, length); 

        	// spinlock release
        	write_unlock(&hello_data.proc_hello_rw);
        	printk(KERN_ALERT "2470:5.3a: release write lock!\n");
	}

	// check for copy_from_user error here (immediately upon sem release)
	if (err) 
		return -EFAULT;

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

	// initialize spin lock
	hello_data.proc_hello_sp=SPIN_LOCK_UNLOCKED;
	hello_data.proc_hello_rw=RW_LOCK_UNLOCKED;

	hello_data.proc_hello_flag=0;

        // module init message
        printk(KERN_ALERT "2470:5.3a: main initialized!\n");
	return 0;
}

static void my_exit (void) {

	if (proc_hello)
		remove_proc_entry (HELLO, proc_mydev);
	if (proc_mydev)
		remove_proc_entry (MYDEV, 0);

        // module exit message
        printk(KERN_ALERT "2470:5.3a: main destroyed!\n");
}

module_init (my_init);
module_exit (my_exit);
