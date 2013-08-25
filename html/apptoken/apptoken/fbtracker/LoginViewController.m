//
//  LoginViewController.m
//  fbtracker
//
//  Created by Yisheng Jiang on 4/11/13.
//  Copyright (c) 2013 Yisheng Jiang. All rights reserved.
//

#import "LoginViewController.h"
#import <FacebookSDK/FacebookSDK.h>
#import "AppDelegate.h"
#import "FriendsViewController.h"

@interface LoginViewController ()
@property (strong, nonatomic) NSArray* topFriends;
@property (strong, nonatomic) NSArray* activeFriends;

@end

@implementation LoginViewController

@synthesize fbLogin;
@synthesize topFriends;
@synthesize activeFriends;
@synthesize spinner;
@synthesize activityIndictator;
@synthesize allFriends;
@synthesize friendPickerController;
- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (IBAction)authBtnAction:(id)sender {
    AppDelegate *appDelegate = [[UIApplication sharedApplication] delegate];
    // The user has initiated a login, so call the openSession method
    // and show the login UX if necessary.
    [self.activityIndictator stopAnimating];

    if (FBSession.activeSession.isOpen) {
        self.activeFriends = nil;
        self.topFriends=nil;
        [appDelegate closeSession];
    } else {
        // The person has initiated a login, so call the openSession method
        // and show the login UX if necessary.
        [appDelegate openSessionWithAllowLoginUI:YES];
    }
}
- (IBAction) fetchMostActive:(id)sender{
    if(FBSession.activeSession.isOpen==NO){
        UIAlertView *alert = [[UIAlertView alloc]
                              initWithTitle: @"Login Required"
                              message: @"Please Login with Facebook"
                              delegate: nil
                              cancelButtonTitle: @"Ok"
                              otherButtonTitles:nil
                              , nil];
        [alert show];

    }
    // Query to fetch the active user's friends, limit to 25.
    if(self.activeFriends != nil){
        [self showFriends:self.activeFriends showModule:@"active"];
    }else{
        [self.activityIndictator startAnimating];

    NSString *query =
    @"SELECT uid, name, pic_square, likes_count FROM user WHERE uid IN "
    @"(SELECT uid2 FROM friend WHERE uid1 = me()) and likes_count order by likes_count desc limit 50";
    NSDictionary *queryParam = [NSDictionary dictionaryWithObjectsAndKeys:
                                query, @"q", nil];
    [FBRequestConnection startWithGraphPath:@"/fql"
                                 parameters:queryParam
                                 HTTPMethod:@"GET"
                          completionHandler:^(FBRequestConnection *connection,
                                              id result,
                                              NSError *error) {
                              if (error) {
                                  NSLog(@"Error: %@", [error debugDescription]);
                              } else {
                                  self.activeFriends=(NSArray *) [result objectForKey:@"data"];

                                  [self showFriends:self.activeFriends showModule:@"active"];
                              }
                          }];
    }
}
- (IBAction)fetchAllFriends:(id)sender{
    if (self.friendPickerController == nil) {
        // Create friend picker, and get data loaded into it.
        self.friendPickerController = [[FBFriendPickerViewController alloc] init];
        self.friendPickerController.title = @"Your friends";
        self.friendPickerController.delegate = self;
    }
    
    [self.friendPickerController loadData];
    [self.friendPickerController clearSelection];
    
    // iOS 5.0+ apps should use [UIViewController presentViewController:animated:completion:]
    // rather than this deprecated method, but we want our samples to run on iOS 4.x as well.
    [self presentModalViewController:self.friendPickerController animated:YES];
   
}
- (IBAction) fetchMostPopular:(id)sender{
    if(FBSession.activeSession.isOpen==NO){
        UIAlertView *alert = [[UIAlertView alloc]
                              initWithTitle: @"Login Required"
                              message: @"Please Login with Facebook"
                              delegate: nil
                              cancelButtonTitle: @"Ok"
                              otherButtonTitles:nil
                              , nil];
        [alert show];
        
    }
    if(self.topFriends != nil){
        [self showFriends:self.topFriends showModule:@"popular"];
    }else{
        [self.activityIndictator startAnimating];
    // Query to fetch the active user's friends, limit to 25.
    NSString *query =
    @"SELECT uid, name, pic_square, friend_count FROM user WHERE uid IN "
    @"(SELECT uid2 FROM friend WHERE uid1 = me()) order by friend_count desc limit 50";
    NSDictionary *queryParam = [NSDictionary dictionaryWithObjectsAndKeys:
                                query, @"q", nil];
    [FBRequestConnection startWithGraphPath:@"/fql"
                                 parameters:queryParam
                                 HTTPMethod:@"GET"
                          completionHandler:^(FBRequestConnection *connection,
                                              id result,
                                              NSError *error) {
                              if (error) {
                                  NSLog(@"Error: %@", [error debugDescription]);
                              } else {
                                  NSArray *friendInfo = (NSArray *) [result objectForKey:@"data"];
                                  self.topFriends=friendInfo;
                                  [self showFriends:self.topFriends showModule:@"popular"];
                              }
                          }];
    }
}
- (void)viewDidLoad
{
    [super viewDidLoad];
    [[NSNotificationCenter defaultCenter]
     addObserver:self
     selector:@selector(sessionStateChanged:)
     name:FBSessionStateChangedNotification
     object:nil];
    
    AppDelegate *appDelegate = [[UIApplication sharedApplication] delegate];
    [ appDelegate openSessionWithAllowLoginUI:NO];
    if(FBSession.activeSession.isOpen){
        
    }
    [self.activityIndictator stopAnimating];
	// Do any additional setup after loading the view.
}

- (void) showFriends:(NSArray *)friendData showModule:(NSString *)module
{
    [self.activityIndictator stopAnimating];

    // Set up the view controller that will show friend information
    FriendsViewController *viewController =
    [[FriendsViewController alloc] initWithStyle:UITableViewStylePlain];
    viewController.data = friendData;
    viewController.module=module;
    // Present view controller modally.
    if ([self
         respondsToSelector:@selector(presentViewController:animated:completion:)]) {
        [self presentViewController:viewController animated:YES completion:nil];
    } else {
        [self presentModalViewController:viewController animated:YES];
    }
}
- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    [[NSNotificationCenter defaultCenter] removeObserver:self];

    // Dispose of any resources that can be recreated.
}
- (void)sessionStateChanged:(NSNotification*)notification {
    if (FBSession.activeSession.isOpen) {
        [self.fbLogin setTitle:@"logout" forState:UIControlStateNormal];
    } else {
        [self.fbLogin setTitle:@"login" forState:UIControlStateNormal];
    }
}





- (void)facebookViewControllerDoneWasPressed:(id)sender {
//    NSMutableString *text = [[NSMutableString alloc] init];
//    
//    // we pick up the users from the selection, and create a string that we use to update the text view
//    // at the bottom of the display; note that self.selection is a property inherited from our base class
//    for (id<FBGraphUser> user in self.friendPickerController.selection) {
//        if ([text length]) {
//            [text appendString:@", "];
//        }
//        [text appendString:user.name];
//    }
//    
    [self dismissModalViewControllerAnimated:YES];
}
- (void)facebookViewControllerCancelWasPressed:(id)sender {
    [self dismissModalViewControllerAnimated:YES];
}

- (void)viewDidUnload {
    [self setActivityIndictator:nil];
    [super viewDidUnload];
}

@end
