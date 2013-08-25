// Example 8.3	:  __get_free_page(), free_page
//		:  main.c

//  

#include <linux/init.h>
#include <linux/module.h>
#include <linux/kernel.h>
#include <linux/version.h>
#include <linux/proc_fs.h>
#include <asm/uaccess.h>

#include <linux/sched.h>
#include <linux/vmalloc.h>  // Note:  what happens if this line is not included ?  Does the pgm compile

MODULE_LICENSE("GPL");

#define PROC_HELLO_LEN 8
#define PROC_HELLO_BUFLEN 1024
#define HELLO "hello"
#define MYDEV "MYDEV"

struct proc_hello_data {
	char proc_hello_name[PROC_HELLO_LEN + 1];
	char *proc_hello_value;
	char proc_hello_flag;
};

static struct proc_hello_data *hello_data;

static struct proc_dir_entry *proc_hello;
static struct proc_dir_entry *proc_mydev;

static int read_hello (char *buf, char **start, off_t offset, 
		int len, int *eof, void *unused) 
{
	struct proc_hello_data *usrsp=hello_data;

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
				"Hello from process %d\njiffies=%ld\n", 
				(int)current->pid,jiffies));
}

static int write_hello (struct file *file,const char * buf, 
		unsigned long count, void *data) 
{
	int length=count;
	struct proc_hello_data *usrsp=hello_data;

	length = (length<PROC_HELLO_LEN)? length:PROC_HELLO_LEN;

	if (copy_from_user(usrsp->proc_hello_value, buf, length)) 
		return -EFAULT;

	usrsp->proc_hello_value[length-1]=0;
	usrsp->proc_hello_flag=1;
	return(length);
}

static int my_init (void) {
	char *page;

  // module init message
  printk(KERN_ALERT "2470:8.3: main initialization!\n");

	proc_mydev = proc_mkdir(MYDEV,0);

	proc_hello = create_proc_entry(HELLO,0,proc_mydev);
	proc_hello->read_proc = read_hello;
	proc_hello->write_proc = write_hello;

#if LINUX_VERSION_CODE <= KERNEL_VERSION(2,6,29)
	proc_hello->owner = THIS_MODULE;
#endif
	
	page =(char *) __get_free_page(GFP_KERNEL);

	if (!page)
		return -ENOMEM;

	// dangerous.  however, to demonstrate the point here.
	hello_data=(struct proc_hello_data *)page;

	// memory definition
	hello_data->proc_hello_value=(char *)vmalloc(PROC_HELLO_BUFLEN);
	
	hello_data->proc_hello_flag=0;

  // module init message
  printk(KERN_ALERT "2470:8.3: memory allocated!\n");
	return 0;
}

static void my_exit (void) {
	// free memory
	vfree(hello_data->proc_hello_value);
	free_page((unsigned long)hello_data);
  // module free message
  printk(KERN_ALERT "2470:8.3: memory freed!\n");

	// free proc entries here
	if (proc_hello)
		remove_proc_entry (HELLO, proc_mydev);
	if (proc_mydev)
		remove_proc_entry (MYDEV, 0);

        // module exit message
        printk(KERN_ALERT "2470:8.3: main destroyed!\n");
}

module_init (my_init);
module_exit (my_exit);
