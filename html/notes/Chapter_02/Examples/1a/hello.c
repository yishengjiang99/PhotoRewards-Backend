// Example# 2.1 .. Simple "Hello World!" module example 

/* #define MODULE 
	.. is no longer necessary as the new 
	kernel build system automatically defines it for you */

#include <linux/init.h>
#include <linux/module.h>
#include <linux/kernel.h>

MODULE_AUTHOR("Me"); 
MODULE_LICENSE("GPL"); 	/* kernel isn't tainted .. SUSE noop */

static int __init hello_init(void){  // qualifiers makes the kernel understand that this is a native hook to init
	printk(KERN_ALERT "Hello, world\n");
	return 0;
}

static void __exit hello_exit(void){
	printk(KERN_ALERT "Goodbye, cruel world\n");
}

module_init(hello_init); //driver initialization entry point
module_exit(hello_exit);
