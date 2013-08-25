/*  CDDapp.c */

#include <errno.h>
#include <fcntl.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/ioctl.h>
#include <sys/mman.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <unistd.h>

// /*    */
// IOCTL commands ... 
#define READ 0
#define WRITE 1
// 


#define MYNUM 5558
#define MYSTR "Eureka!"

int main() {
	int fd, len, wlen;
	char str[128];
	long inum = MYNUM;
	long onum = 0;

	int sz = 512;
	int st = 0;

	char *m;
	pid_t pid;

	int c;

	strcpy(str, MYSTR);

	// open 
	if((fd = open("/dev/CDD", O_RDWR)) == -1) {
		fprintf(stderr,"ERR:on open():%s\n",strerror(errno));
		exit(0);
	}

	// write 
	wlen = strlen(str);
	if ((len = write(fd, str, wlen)) == -1) {
		fprintf(stderr,"ERR:on write():%s\n",strerror(errno));
		exit(1);
	}

	// read 
	if ((len = read(fd, str, sizeof(str))) == -1) {
		fprintf(stderr,"ERR:on read():%s\n",strerror(errno));
		exit(1);
	}
	fprintf(stdout, "%s\n", str);

	// write using ioctl()
	// if ((len = ioctl(fd, WRITE, &inum)) == -1) {
	//	fprintf(stderr,"ERR:on ioctl-write():%s\n",strerror(errno));
	//	exit(1);
	// }

	// read using ioctl()
	if ((len = ioctl(fd, READ, &onum)) == -1) {
		fprintf(stderr,"ERR:on ioctl-read():%s\n",strerror(errno));
		exit(1);
	}
	fprintf(stdout, "read .. %#0x(%d)\n", onum,onum);


	printf("Test mmap...\n");

	// mmap
	m = mmap((void *)onum, sz, PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, fd, 0);

	if ((pid=fork()) == 0) {
		sprintf(m, "Hello from child, pid %d", getpid());
	} 
	else {
		sleep(1);
		printf ("Hello from parent, pid %d\n", getpid());
		wait(&st);
		printf ("Replaying msg from child: %s\n", m);
	  fprintf(stderr,"Enter a char(PID==%d)\n",getpid());
	  c=getchar();
	}

	close(fd);

	return 0;
}
