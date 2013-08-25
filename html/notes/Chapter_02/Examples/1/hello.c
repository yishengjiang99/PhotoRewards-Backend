// Example# 2.1 .. Simple "Hello World!" module example 

/* #define MODULE 
	.. is no longer necessary as the new 
	kernel build system automatically defines it for you */

#include <linux/init.h>
#include <linux/module.h>
#include <linux/kernel.h>
#include <linux/sched.h> // for current macro

MODULE_AUTHOR("Me"); 
MODULE_LICENSE("GPL"); 	/* kernel isn't tainted .. SUSE noop */

static int hello_init(void){
	printk(KERN_ALERT "Hello, world (PID=%d)\n", current->pid); //prints to /var/log/message
	return 0;
}

static void hello_exit(void){
	printk(KERN_ALERT "Goodbye, cruel world (PID=%d)\n",current->pid);
}

module_init(hello_init);
module_exit(hello_exit);
