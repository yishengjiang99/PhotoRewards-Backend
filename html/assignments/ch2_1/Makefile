OBJ := main
KDIR := /lib/modules/$(shell uname -r)/build
PWD := $(shell pwd)
LDD := /lib/modules/$(shell uname -r)/kernel/drivers/ldd/

obj-m :=$(OBJ).o

compile:
	@$(MAKE) -C $(KDIR) M=$(PWD) modules
load:	
	compile
	 su -c "insmod ./$(OBJ).ko input=3"
unload: 
	-su -c "rmmod $(OBJ)"
	-@rm -fr $(OBJ).o $(OBJ).ko $(OBJ).*.* .tmp_versions* [mM]odule*
