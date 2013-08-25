// Example 4.1:  to demo reads from /proc file
//    		 main.c

#include <linux/module.h>
#include <linux/version.h>
#include <linux/kernel.h>
#include <linux/proc_fs.h>
#include <linux/init.h>

MODULE_LICENSE("GPL");

static struct proc_dir_entry *proc_hello;

static int read_hello (char *buf, char **start, off_t offset, 
			int len, int *eof, void *unused) {
	*eof = 1;
	return(sprintf(buf,"Hello at %d\n", (int)jiffies));
}

static int my_init (void) {
	proc_hello = create_proc_entry("hello",0,0);
	proc_hello->read_proc = read_hello;

    #if LINUX_VERSION_CODE <= KERNEL_VERSION(2,6,29)
        proc_hello->owner = THIS_MODULE;
    #endif      

	return 0;
}

static void my_exit (void) {
	if (proc_hello)
		remove_proc_entry ("hello", 0);
}

module_init (my_init);
module_exit (my_exit);
