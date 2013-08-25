#include <linux/version.h>
#if LINUX_VERSION_CODE > KERNEL_VERSION(2,6,36)
#include <generated/utsrelease.h>
#else
#include <linux/utsrelease.h>
#endif

#include <linux/init.h>
#include <linux/module.h>
#include <linux/kernel.h>
#include <linux/errno.h>
#include <linux/fs.h>
#include <linux/mm.h>
#include <asm/uaccess.h>

#define NPAGES 16

MODULE_LICENSE("GPL");   /*  Kernel isn't tainted .. but doesn't 
			     it doesn't matter for SUSE anyways :-(  
			     
			     they have a concept of vendor-tainted ..
                             meaning that no one can call them ... 
			 */

#define CMD0 0
#define CMD1 1

#define CMD10 10
#define CMD11 11

static unsigned int counter = 0;
#if LINUX_VERSION_CODE > KERNEL_VERSION(2,6,36)
static char *string;
#else
static char string [PAGE_SIZE*NPAGES];
#endif

static int CDD_open (struct inode *inode, struct file *file)
{
	// MOD_INC_USE_COUNT;
	return 0;
}

static int CDD_release (struct inode *inode, struct file *file)
{
	// MOD_DEC_USE_COUNT;
	return 0;
}

static ssize_t CDD_read (struct file *file, char *buf, 
size_t count, loff_t *ppos)
{
	int len, err;
	if(counter <= 0) return 0;

	err = copy_to_user(buf,string,counter);

	if (err != 0) return -EFAULT;

	len = counter;
	counter = 0;
	return len;
}

static ssize_t CDD_write (struct file *file, const char *buf, 
size_t count, loff_t *ppos)
{
	int err;
	err = copy_from_user(string,buf,count);
	if (err != 0) return -EFAULT;

	counter += count;
	return count;
}


#if LINUX_VERSION_CODE >= KERNEL_VERSION(3,0,0)
static long CDD_ioctl(struct file *file,unsigned int cmd, unsigned long arg)
#else
static int CDD_ioctl(struct inode *inode, struct file *file,
unsigned int cmd, unsigned long arg)
#endif
{
	int retval = 0;
	switch ( cmd ) {
		case CMD0: // for reading data from arg 
			if (!access_ok(VERIFY_READ, (void __user *) arg, sizeof(int)))
				return -EFAULT;

			if (copy_to_user((int *)arg, (unsigned int*)&string, sizeof(int)))
				return -EFAULT;
			break;
		case CMD1: // for writing data to arg
			if (!access_ok(VERIFY_WRITE, (void __user *) arg, sizeof(int)))
				return -EFAULT;

			if (copy_from_user((int *)string, (int *)arg, sizeof(int)))
				return -EFAULT;
			break;
		case CMD10: // for setting data from arg 
			if (!access_ok(VERIFY_READ, (void __user *) arg, sizeof(int)))
				return -EFAULT;

			if (copy_to_user((int *)arg, (int*)&counter, sizeof(int)))
				return -EFAULT;
			break;
		case CMD11: // for writing data to arg
			if (!access_ok(VERIFY_WRITE, (void __user *) arg, sizeof(int)))
				return -EFAULT;

			counter = arg;
			break;
		default:
			retval = -EINVAL;
	}
	return retval;
}

int mmap_vmaddr(struct file *filp, struct vm_area_struct *vma)
{
// Adapted from http://www.scs.ch/~frey/linux/mmap.example.tar
	int retval;
 	unsigned long len = vma->vm_end - vma->vm_start;
  unsigned long st= vma->vm_start;
  char *vmptr = (char *)string;
  unsigned long pfn;

	// check if len requested is greater than mmap'able/allocated
  if (len > NPAGES * PAGE_SIZE)
     return -EIO;

  // map multiple/discontiguous physical pages individually 
  while (len> 0) {
  	pfn = vmalloc_to_pfn(vmptr);
    if ((retval = remap_pfn_range(vma,st,pfn,PAGE_SIZE,PAGE_SHARED)) < 0) 
			return retval;
                
		st += PAGE_SIZE;
		vmptr += PAGE_SIZE;
		len -= PAGE_SIZE;
	}
	return 0;
}


int CDD_mmap (struct file *file, struct vm_area_struct *vma)
{
	// only offset 0 
 	if (vma->vm_pgoff == 0) {
 		return mmap_vmaddr(file, vma);
	}

  return -EIO;
}

static struct file_operations CDD_fops =
{
	// for LINUX_VERSION_CODE 2.4.0 and later 
	owner:	THIS_MODULE, 	// struct module *owner
	open:	CDD_open, 	// open method 
	read:   CDD_read,	// read method 
	write:  CDD_write, 	// write method 
	release:  CDD_release, 	// release method
#if LINUX_VERSION_CODE >= KERNEL_VERSION(3,0,0)
	unlocked_ioctl:  CDD_ioctl, 	// ioctl method
#else
	ioctl:  CDD_ioctl, 			// ioctl method
#endif
	mmap:   CDD_mmap
};

static int CDD_init(void)
{
	int i;
	i = register_chrdev (32, "CDD", & CDD_fops);
	if (i != 0) return - EIO;

#if LINUX_VERSION_CODE > KERNEL_VERSION(2,6,36)
	string=vmalloc(PAGE_SIZE*NPAGES); 
#endif

	printk("string==%d(%x)",(int)string,(unsigned int)string);

	/* mark the pages reserved */
 	for (i = 0; i < NPAGES * PAGE_SIZE; i+= PAGE_SIZE) {
 		SetPageReserved(vmalloc_to_page((void *)(((unsigned long)string) + i)));      
	}

	return 0;
}

static void CDD_exit(void)
{
	int i=0;
	/* unreserve the pages */
 	for (i = 0; i < NPAGES * PAGE_SIZE; i+= PAGE_SIZE) {
 		ClearPageReserved(vmalloc_to_page((void *)(((unsigned long)string) + i)));
	}

#if LINUX_VERSION_CODE > KERNEL_VERSION(2,6,36)
	vfree(string);
#endif
	unregister_chrdev (32, "CDD");
}

module_init(CDD_init);
module_exit(CDD_exit);

