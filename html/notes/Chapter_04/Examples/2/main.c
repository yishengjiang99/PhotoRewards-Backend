// Example 4.2	:  To demo writes to /proc file
//		:  main.c

#include <linux/init.h>
#include <linux/module.h>
#include <linux/version.h>
#include <linux/kernel.h>
#include <linux/proc_fs.h>
#include <asm/uaccess.h>

#include <linux/sched.h>

MODULE_LICENSE("GPL");

#define PROC_HELLO_LEN 8

struct proc_hello_data {
	char proc_hello_name[PROC_HELLO_LEN + 1];
	char *proc_hello_value;
	char proc_hello_flag;
};

static struct proc_hello_data hello_data;

static struct proc_dir_entry *proc_hello;

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
	struct proc_hello_data *usrsp=&hello_data;

	length = (length<PROC_HELLO_LEN)? length:PROC_HELLO_LEN;

	if (copy_from_user(usrsp->proc_hello_value, buf, length)) 
		return -EFAULT;

        // module debug message
        // printk(KERN_ALERT "2470-020:4.2: got length %d!\n", length);

	usrsp->proc_hello_value[length-1]=0;
	usrsp->proc_hello_flag=1;
	return(length);
}

static int my_init (void) {
	hello_data.proc_hello_value=vmalloc(4096);
	proc_hello = create_proc_entry("hello",0,0);
	proc_hello->read_proc = read_hello;
	proc_hello->write_proc = write_hello;

 	#if LINUX_VERSION_CODE <= KERNEL_VERSION(2,6,29)
		proc_hello->owner = THIS_MODULE;
	#endif      

	hello_data.proc_hello_flag=0;

 	// module init message
	printk(KERN_ALERT "2470-020:4.2: main initialized!\n");
	return 0;
}

static void my_exit (void) {
	vfree(hello_data.proc_hello_value);
	if (proc_hello)
		remove_proc_entry ("hello", 0);

	// module exit message
	printk(KERN_ALERT "2470-020:4.2: main destroyed!\n");
}

module_init (my_init);
module_exit (my_exit);
