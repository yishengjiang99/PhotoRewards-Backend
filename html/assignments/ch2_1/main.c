#include <linux/init.h>
#include <linux/kernel.h>
#include <linux/module.h>
#include <linux/version.h>
#if LINUX_VERSION_CODE>=KERNEL_VERSION(2,6,0)
#include <linux/moduleparam.h>
#endif

MODULE_AUTHOR("me");
MODULE_LICENSE("GPL");

static int input=0;

module_param(input,int,0);


static int mod_init(void){
	printk(KERN_ALERT "main init with param: %d",input);
	return 0;
}
static void mod_exit(void){
	printk(KERN_ALERT "main exits");
}

module_init(mod_init);
module_exit(mod_exit);
