//
//  TableViewController.h
//  PictureOfTheDay
//
//  Created by Yisheng Jiang on 4/14/13.
//  Copyright (c) 2013 AppLoot. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "UploadViewController.h"
@interface TableViewController : UITableViewController <UIAlertViewDelegate,UIImagePickerControllerDelegate,UINavigationBarDelegate>
@property (strong, nonatomic)NSArray *listOfItems;
@property (strong, nonatomic)NSDictionary *configs;
@property (strong, nonatomic)NSDictionary *mystats;
@property (strong, nonatomic)NSArray *myuploads;


@end
