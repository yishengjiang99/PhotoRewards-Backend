//
//  LoginViewController.h
//  fbtracker
//
//  Created by Yisheng Jiang on 4/11/13.
//  Copyright (c) 2013 Yisheng Jiang. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <FacebookSDK/FacebookSDK.h>
@interface LoginViewController : UIViewController<FBFriendPickerDelegate>
- (IBAction)authBtnAction:(id)sender;
@property (retain, nonatomic) IBOutlet UIButton *fbLogin;
- (IBAction) fetchMostPopular:(id)sender;
@property (retain, nonatomic) IBOutlet UIButton *spinner;
@property (retain, nonatomic) IBOutlet UIActivityIndicatorView *activityIndictator;
@property (weak, nonatomic) IBOutlet UIButton *allFriends;
- (IBAction)fetchAllFriends:(id)sender;
@property (strong, nonatomic) FBFriendPickerViewController *friendPickerController;

@end
