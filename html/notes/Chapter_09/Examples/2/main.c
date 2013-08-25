// Example 9.2 	:  kmem_cache_create()
// 		:  kmem_cache_destroy()
// 		:  kmem_cache_alloc()
// 		:  kmem_cache_free()
//		:  main.c

#define thisSLEEP 1000

#include <linux/init.h>
#include <linux/module.h>
#include <linux/kernel.h>
#include <linux/version.h>
#include <linux/proc_fs.h>
#include <asm/uaccess.h>
#include <asm/io.h>
#include <asm/div64.h>

#include <linux/sched.h>
#include <linux/vmalloc.h>  // Note:  what happens if this line is not included ?  Does the pgm compile
#include <linux/delay.h>

MODULE_LICENSE("GPL");

#define PROC_HELLO_NUMENTRIES 20
#define PROC_HELLO_LEN 8
#define PROC_HELLO_BUFLEN 1024
#define HELLO "hello"
#define MYDEV "MYDEV"

struct proc_hello_data {
	char proc_hello_name[PROC_HELLO_LEN + 1];
	char *proc_hello_value;
};

static struct kmem_cache *hello_cache;
static char proc_hello_flag=0;

static struct proc_hello_data *hello_data;

static struct proc_dir_entry *proc_hello;
static struct proc_dir_entry *proc_mydev;

int order=2;

static int read_hello (char *buf, char **start, off_t offset, 
		int len, int *eof, void *unused) 
{
	int n=0;
	struct proc_hello_data *usrsp=hello_data;

	// from /proc/ioports timer0=>0040-0043
	volatile unsigned long long tt=0, tt2=0; 
	volatile unsigned long ttr=0;
	unsigned long rem=0, cpusp=0;

	volatile signed long j1,j2;

	*eof = 1;

	if (offset) { n=0; }
	else if (proc_hello_flag) {

	  j1=jiffies;
	  tt = inl(0x40); 		// units is 1/MHz .. uSec
		mdelay(thisSLEEP);	// units is 1us i.e. 1/MHz
	  tt2 = inl(0x40);		// units is 1/MHz .. uSec
		j2=jiffies;

		proc_hello_flag=0;
	  ttr=(tt2>tt)?tt2-tt:((0xfffffffe)-tt)+tt2;			// prep for 64bit div
		// rem = do_div(ttr,1024*1024*1024);  // doing 64bit div
		cpusp = ttr / (1024*1024);
		
		n=sprintf(buf, "Hello .. I got \"%s\":\n\t[ CPUSpeed=%ld MHz (tt2=%lld; tt=%lld; j2=%ld; j1=%ld)].\n", 
				usrsp->proc_hello_value,cpusp, tt2,tt,j2,j1);

		vfree(usrsp->proc_hello_value);
		kmem_cache_free(hello_cache,hello_data);
	}
	else
		n=sprintf(buf,"Hello from process %d\njiffies=%ld\n", 
				(int)current->pid,jiffies);

	wmb();
	
	return n;
}

static int write_hello (struct file *file,const char * buf, 
		unsigned long count, void *data) 
{
	int length=count;

	hello_data=kmem_cache_alloc(hello_cache,GFP_KERNEL);

	proc_hello_flag=0;

	// memory definition
	hello_data->proc_hello_value=(char *)vmalloc(PROC_HELLO_BUFLEN);

	length = (length<PROC_HELLO_LEN)? length:PROC_HELLO_LEN;

	if (copy_from_user(hello_data->proc_hello_value, buf, length))  {
		vfree(hello_data->proc_hello_value);
		kmem_cache_free(hello_cache,hello_data);
		return -EFAULT;
	}

	rmb();

	hello_data->proc_hello_value[length-1]=0;
	proc_hello_flag=1;
	return(length);
}

static int my_init (void) {
	
	hello_cache = kmem_cache_create("proc hello cache",
				sizeof(*hello_data)*
					PROC_HELLO_NUMENTRIES,
				0,
				SLAB_HWCACHE_ALIGN,
				NULL);

	if (!hello_cache)
		return -ENOMEM;


	proc_mydev = proc_mkdir(MYDEV,0);

	proc_hello = create_proc_entry(HELLO,0,proc_mydev);
	proc_hello->read_proc = read_hello;
	proc_hello->write_proc = write_hello;

#if LINUX_VERSION_CODE <= KERNEL_VERSION(2,6,29)
	proc_hello->owner = THIS_MODULE;
#endif
	
        // module init message
        printk(KERN_ALERT "2470:9.2: main initialized!\n");
	return 0;
}

static void my_exit (void) {
	// free memory
	kmem_cache_destroy(hello_cache);

	// free proc entries here
	if (proc_hello)
		remove_proc_entry (HELLO, proc_mydev);
	if (proc_mydev)
		remove_proc_entry (MYDEV, 0);

        // module exit message
        printk(KERN_ALERT "2470:9.2: main destroyed!\n");
}

module_init (my_init);
module_exit (my_exit);
