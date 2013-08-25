// Example 10.6a	:  workqueue
//		:  main.c
//  

// select or non-shared IRQ handler
// #define NONSHARED_IRQHANDLER
#define SHARED_IRQHANDLER

// delay of 3-sec
#define thisDELAY 3*HZ
// #define thisDELAY 50


#include <linux/version.h>
#include <linux/init.h>
#include <linux/module.h>
#include <linux/kernel.h>
#include <linux/proc_fs.h>
#include <asm/uaccess.h>

#include <linux/sched.h>
#include <linux/irq.h>
#include <linux/interrupt.h>

#include <linux/workqueue.h> // workqueue

#include <linux/slab.h>  // since 2.6.29
// see http://lwn.net/Articles/319686/

MODULE_LICENSE("GPL");

#define PROC_HELLO_LEN 8
#define PROC_HELLO_BUFLEN 1024
#define HELLO "hello"
#define MYDEV "MYDEV"

#ifdef NONSHARED_IRQHANDLER
		#define thisIRQ 13
		#define MYIRQFLAGS 0		// not shared 
#endif

#ifdef SHARED_IRQHANDLER
	#define thisIRQ 12				// map to psmouse IRQ
	#if LINUX_VERSION_CODE >= KERNEL_VERSION(2,6,24)
		#define MYIRQFLAGS IRQF_SHARED	// shared 
	#else
		#define MYIRQFLAGS SA_SHIRQ // shared */
	#endif
#endif

struct proc_hello_data {
	char proc_hello_name[PROC_HELLO_LEN + 1];
	char *proc_hello_value;
	char proc_hello_flag;
	struct workqueue_struct *proc_hello_wkq;
	struct delayed_work *wk;
};

/*  just for quick reference
struct delayed_work {
    struct work_struct work;
    struct timer_list timer;
};
*/

unsigned long irqflags=0;
int dev_id = 128;

static void write_hello_wk(struct work_struct *work);
static struct proc_hello_data *hello_data;

static struct proc_dir_entry *proc_hello;
static struct proc_dir_entry *proc_mydev;

irqreturn_t irq_handler(int irq, void *dev_id,struct pt_regs *regs)
{
	static int initialized=0;
    struct proc_hello_data *usrsp=hello_data;

    int *id=(int *)dev_id;

    if (*id!=128) {
		printk(KERN_INFO "interrupt id=%d irq=%d\n",*id,irq);
        return IRQ_HANDLED;
	}
	
    mb();

	// INIT_DELAYED_WORK anyways calls INIT_WORK internally
	if (!initialized++) 
		INIT_DELAYED_WORK(hello_data->wk, write_hello_wk); 
	else
		PREPARE_DELAYED_WORK(hello_data->wk, write_hello_wk); 

    queue_delayed_work(usrsp->proc_hello_wkq, usrsp->wk, thisDELAY);
    printk(KERN_ALERT "2470:10.6a: workqueue scheduled!\n");
    return IRQ_HANDLED;
}

static void write_hello_wk(struct work_struct *work) {
	struct proc_hello_data *m=hello_data;

	if (&m->wk->work==work)  {
	
		printk("Printing from workqueue .. I got \"%s\" at jiffies=%ld\n", 
	   m->proc_hello_value, jiffies); 

	}
	else {
		printk("Unexpected Address Mismatch: Bailing Out => Doing Nothing!\n");

		
	}
}
	

static int read_hello (char *buf, char **start, off_t offset, 
		int len, int *eof, void *unused) 
{
	struct proc_hello_data *usrsp=hello_data;
	int n=0;

	*eof = 1;

	if (offset) { n=0; }
	else if (usrsp->proc_hello_flag) {
		usrsp->proc_hello_flag=0;
		n=sprintf(buf,"See /var/log/messages.\n");
	}
	else
		n=sprintf(buf, "Hello from process %d\njiffies=%ld\n", 
				(int)current->pid,jiffies);
	
	return n;
}

static int write_hello (struct file *file,const char * buf, 
		unsigned long count, void *data) 
{
	int length=count;
	struct proc_hello_data *usrsp=hello_data;

	length = (length<PROC_HELLO_BUFLEN)? length:PROC_HELLO_BUFLEN;

	if (copy_from_user(usrsp->proc_hello_value, buf, length)) 
		return -EFAULT;

	usrsp->proc_hello_value[length-1]=0;
	usrsp->proc_hello_flag=1;

	return(length);
}

static int my_init (void) {
	int rc=0;
	proc_mydev = proc_mkdir(MYDEV,0);

	proc_hello = create_proc_entry(HELLO,0,proc_mydev);
	proc_hello->read_proc = read_hello;
	proc_hello->write_proc = write_hello;

	// proc_hello->owner = THIS_MODULE;

	hello_data=(struct proc_hello_data *)
		kmalloc(sizeof(*hello_data),GFP_KERNEL);

	hello_data->proc_hello_value=(char *)
		kmalloc(PROC_HELLO_BUFLEN,GFP_KERNEL);

	// no need for a separate memory allocation for wkq.
	// create_workqueue sets it up
	// hello_data->proc_hello_wkq=(struct workqueue_struct *)
		// kmalloc(sizeof(struct workqueue_struct),GFP_KERNEL);
	hello_data->proc_hello_wkq=create_workqueue(HELLO);
	
	hello_data->wk=(struct delayed_work *)
	    kzalloc(sizeof(struct delayed_work),GFP_KERNEL);

	hello_data->proc_hello_flag=0;

    irqflags=MYIRQFLAGS;

    // #if LINUX_VERSION_CODE > KERNEL_VERSION(2,6,30)
        //if (!can_request_irq(thisIRQ, irqflags))
        //    free_irq(thisIRQ,(void*)&dev_id);
    // #endif

        // setup interrupt handler
   	if ((rc=(request_irq(thisIRQ,
        	(irq_handler_t) irq_handler,
       		irqflags,
       		(char *)"MYDEV_irq_handler",
       		(void*) &dev_id)))<0) {
   		printk(KERN_ALERT "2470:10.6a: intr not initialized!(RC=%d)\n",rc);
			goto err;
	}
	else {
   		printk(KERN_ALERT "2470:10.6a: intr initialized!\n");
	}

    // module init message
    printk(KERN_ALERT "2470:10.6a: main initialized!\n");
    printk(KERN_ALERT "2470:10.6a: memory allocated(hello_data) = %0x(%d bytes)!\n", (int) hello_data, ksize(hello_data));
    printk(KERN_ALERT "2470:10.6a: memory allocated(hello_data->wk) = %0x(%d bytes)!\n", (int) &hello_data->wk, ksize(hello_data->wk));
    printk(KERN_ALERT "2470:10.6a: memory allocated(hello_data->proc_hello_value) = %d!\n", ksize(hello_data->proc_hello_value));

	return 0;

err:
	while (delayed_work_pending(hello_data->wk)) {
		cancel_delayed_work_sync(hello_data->wk);
        printk(KERN_ALERT "2470:10.6a: Cancelling delayed work!\n");
	}

	destroy_workqueue(hello_data->proc_hello_wkq);

	kfree(hello_data->wk);

	kfree(hello_data->proc_hello_value);
	kfree(hello_data);

	if (proc_hello)
		remove_proc_entry (HELLO, proc_mydev);
	if (proc_mydev)
		remove_proc_entry (MYDEV, 0);

    // module exit message
    printk(KERN_ALERT "2470:10.6a: main bailed out!\n");
	
	return rc;
}

static void my_exit (void) {

    // irq
    // #if LINUX_VERSION_CODE > KERNEL_VERSION(2,6,30)
        // if (!can_request_irq(thisIRQ, irqflags))
    // #endif
   			free_irq(thisIRQ,&dev_id);

	while (delayed_work_pending(hello_data->wk)) {
		cancel_delayed_work_sync(hello_data->wk);
        printk(KERN_ALERT "2470:10.6a: Cancelling delayed work!\n");
	}

	destroy_workqueue(hello_data->proc_hello_wkq);

	kfree(hello_data->wk);

	kfree(hello_data->proc_hello_value);
	kfree(hello_data);

	if (proc_hello)
		remove_proc_entry (HELLO, proc_mydev);
	if (proc_mydev)
		remove_proc_entry (MYDEV, 0);

    // module exit message
    printk(KERN_ALERT "2470:10.6a: main destroyed!\n");
}

module_init (my_init);
module_exit (my_exit);
