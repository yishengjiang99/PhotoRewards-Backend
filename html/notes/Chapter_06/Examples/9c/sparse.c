#include <stdio.h>
#include <fcntl.h>


char *fn="myfile.dat";

int main() {

	char buf[4096]=" ";
	int wfd=0;

	if ((wfd=open(fn,O_WRONLY|O_TRUNC|O_CREAT)) < 0) 
		fprintf(stderr, "open failed on \"%s\"",fn);

	lseek(wfd, 8192, SEEK_SET);
	
	write(wfd,buf,4096);

	close(wfd);

}
