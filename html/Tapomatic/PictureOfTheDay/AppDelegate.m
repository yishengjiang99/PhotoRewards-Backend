//
//  AppDelegate.m
//  PictureOfTheDay
//
//  Created by Yisheng Jiang on 4/14/13.
//  Copyright (c) 2013 AppLoot. All rights reserved.
//

#import "AppDelegate.h"

#import "TableViewController.h"
#import "Util.h"
#import "Reachability.h"
@implementation AppDelegate
@synthesize configs;
- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions
{

    Reachability* wifiReach = [Reachability reachabilityWithHostName: @"www.apple.com"];
    NetworkStatus netStatus = [wifiReach currentReachabilityStatus];
    
    switch (netStatus)
    {
        case NotReachable:
        {
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Oops!" message:@"Cannot Connect To Internet!" delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
            [alert show];
            break;
        }
    }

    self.window = [[UIWindow alloc] initWithFrame:[[UIScreen mainScreen] bounds]];
    // Override point for customization after application launch.
    self.viewController = [[TableViewController alloc] initWithStyle:UITableViewStyleGrouped];
    
    UINavigationController *navigationController = [[UINavigationController alloc]
                                                    initWithRootViewController:self.viewController];
    self.viewController.configs =[Util getConfigs];
    self.configs=self.viewController.configs;
    self.window.rootViewController =navigationController;
    [self.window makeKeyAndVisible];
    return YES;
}

- (void)applicationWillResignActive:(UIApplication *)application
{
    // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
    // Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
}

- (void)applicationDidEnterBackground:(UIApplication *)application
{
    // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later. 
    // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
}

- (void)applicationWillEnterForeground:(UIApplication *)application
{
    // Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
}

- (void)applicationDidBecomeActive:(UIApplication *)application
{
    // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
}

- (void)applicationWillTerminate:(UIApplication *)application
{
    // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
}
- (void)application:(UIApplication*)application didRegisterForRemoteNotificationsWithDeviceToken:(NSData*)deviceToken
{
    NSString *tokenStr = [[deviceToken description] stringByTrimmingCharactersInSet:[NSCharacterSet characterSetWithCharactersInString:@"<>"]];
    
    tokenStr = [tokenStr stringByReplacingOccurrencesOfString:@" " withString:@""];
    NSString *mac=[self.viewController.configs objectForKey:@"mac"];
    NSString *url=[NSString stringWithFormat:@"http://www.apploot.com/token.php?mac=%@&token=%@",mac,tokenStr];
    NSURLRequest *request = [NSURLRequest requestWithURL:[NSURL URLWithString:url]];
    [[NSURLConnection alloc] initWithRequest:request delegate:nil];
	NSLog(@"My token is: %@", deviceToken);
    
}
- (NSString *) getUserId{
    return [self.configs objectForKey:@"mac"];
}
- (void)application:(UIApplication*)application didFailToRegisterForRemoteNotificationsWithError:(NSError*)error
{
	NSLog(@"Failed to get token, error: %@", error);
}

@end
