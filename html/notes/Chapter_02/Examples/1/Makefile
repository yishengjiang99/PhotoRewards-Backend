KDIR := /lib/modules/$(shell uname -r)/build
PWD := $(shell pwd)

OBJ := hello

obj-m := $(OBJ).o  #compile as a module.

all: load clean

run:  load unload

compile:
	@$(MAKE) -C $(KDIR) M=$(PWD) modules 

###  
###  Alternatively, you may want to use the early 2.6 syntax of
###  $(MAKE) -C $(KDIR) SUBDIRS=$(PWD) modules 
###  
###

load:	compile
	@echo "try \"tail -f /var/log/messages\" in another window(as root) ..";
	su -c "insmod ./$(OBJ).ko" #insert a moduel into a kernel.. 

unload: 
	-su -c "rmmod $(OBJ)" 

clean: unload
	-@rm -fr $(OBJ).o $(OBJ).ko $(OBJ).*.* .$(OBJ).* .tmp_versions* [mM]odule*
