//
//  FriendsViewController.h
//  fbtracker
//
//  Created by Yisheng Jiang on 4/10/13.
//  Copyright (c) 2013 Yisheng Jiang. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface FriendsViewController : UITableViewController

@property (strong, nonatomic) NSArray *data;
@property (strong, nonatomic) UIToolbar *toolbar;
@property (strong, nonatomic) NSString* module;

@end
