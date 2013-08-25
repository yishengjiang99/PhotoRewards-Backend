//
//  AppDelegate.h
//  fbtracker
//
//  Created by Yisheng Jiang on 4/10/13.
//  Copyright (c) 2013 Yisheng Jiang. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <FacebookSDK/FacebookSDK.h>
#import "LoginViewController.h"

@class ViewController;
@interface AppDelegate : UIResponder <UIApplicationDelegate>
extern NSString *const FBSessionStateChangedNotification;
- (BOOL)openSessionWithAllowLoginUI:(BOOL)allowLoginUI;
- (void)closeSession;
@property (strong, nonatomic) LoginViewController *viewController;

@property (strong, nonatomic) UIWindow *window;
@end
