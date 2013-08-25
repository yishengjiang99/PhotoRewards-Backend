//
//  TableViewController.m
//  PictureOfTheDay
//
//  Created by Yisheng Jiang on 4/14/13.
//  Copyright (c) 2013 AppLoot. All rights reserved.
//

#import "TableViewController.h"
#import "AsyncImageView.h"
#import "DetailedUIViewController.h"
#import "UploadViewController.h"
#import "Util.h"


@implementation TableViewController
@synthesize listOfItems;
@synthesize configs;

- (id)initWithStyle:(UITableViewStyle)style
{
    self = [super initWithStyle:style];
    if (self) {
        [self.navigationController setNavigationBarHidden:YES];
        self.navigationController.toolbarHidden = YES;
        // Custom initialization
    }
    return self;
}
-(void) viewWillAppear:(BOOL)animated
{
    [self.navigationController setNavigationBarHidden:YES];

    NSDictionary *pics=[Util getJson:[NSString stringWithFormat:@"http://www.apploot.com/pod2.php?uid=%@",
                                      [self.configs objectForKey:@"mac"]]];
    self.myuploads=[pics objectForKey:@"mypics"];
    self.mystats=[pics objectForKey:@"mystats"];
    self.listOfItems=[pics objectForKey:@"categories"];

    [self.tableView reloadData];
    [self.view setNeedsDisplay];
}
- (void)viewDidLoad
{
    [super viewDidLoad];    

    if([self.configs objectForKey:@"gmtitle"]){
        UIAlertView *alert = [[UIAlertView alloc]
                              initWithTitle: [self.configs valueForKey:@"gmtitle"]
                              message: [self.configs valueForKey:@"gm"]
                              delegate: self
                              cancelButtonTitle:@"Ok"
                              otherButtonTitles:@"cancel", nil];
        [alert show];
    }
    // Let the device know we want to receive push notifications
	[[UIApplication sharedApplication] registerForRemoteNotificationTypes:
     (UIRemoteNotificationTypeBadge | UIRemoteNotificationTypeSound | UIRemoteNotificationTypeAlert)];
    
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    return 2;
}

- (NSString *)tableView:(UITableView *)tableView titleForHeaderInSection:(NSInteger)section
{
        if (section == 0)
        {
            return @"My Stats";
        }
        if (section == 1)
        {
            return @"Picture of The Day";
        }
        return @"else";
}
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    // Return the number of rows in the section.
    if(section==0){
        return 2;
    }else return [self.listOfItems count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *CellIdentifier = @"Cell";
    
    UITableViewCell *cell = [self.tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
        cell.accessoryType=UITableViewCellAccessoryDisclosureIndicator;
    }
    if(indexPath.section==1){
        NSString *cellValue = [self.listOfItems objectAtIndex:indexPath.row];
        cell.textLabel.text = cellValue;
    }else{
        cell.accessoryType=UITableViewCellAccessoryNone;
        if(indexPath.row==0){
            NSString *uploads = [self.mystats objectForKey:@"cnt"];
            cell.textLabel.text=[NSString stringWithFormat:@"%@ Uploads",uploads];
        }else{
            NSString *liked = [self.mystats objectForKey:@"totallikes"];
            cell.textLabel.text=[NSString stringWithFormat:@"%@ Likes",liked];
        }
    }
    
    return cell;
}





#pragma mark - Table view delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    DetailedUIViewController *details=[[DetailedUIViewController alloc] init];
    NSString *category;
    [self.navigationController setNavigationBarHidden:NO];


    if(indexPath.section==1){
        NSString *cellValue = [self.listOfItems objectAtIndex:indexPath.row];
        category=cellValue;
        details.picList=[Util getJsonArray:
                  [NSString stringWithFormat:
                   @"http://www.apploot.com/picofcategory.php?cat=%@",[self encodedURLParameterString:category]]];
        details.navigationItem.title=category;

    }else{
        category=@"Random";
        details.picList=self.myuploads;
        details.navigationItem.title=@"My uploads";
    }
    details.category=category;
    if(details.picList.count>0){
        [details loadPictureByIndex];
    }
    [details reloadInputViews];
    
    [self.navigationController pushViewController:details animated:NO];
}


#pragma mark UIAlertView data sour
-(void) alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex{
    
    //u need to change 0 to other value(,1,2,3) if u have more buttons.then u can check which button was pressed.
    
    if (buttonIndex == 0) {
        NSURL *url = [NSURL URLWithString:[self.configs valueForKey:@"gmurl"]];
        if (![[UIApplication sharedApplication] openURL:url])
            NSLog(@"%@%@",@"Failed to open url:",[url description]);
    }
}
     - (NSString *) encodedURLParameterString:(NSString *)input
    {
        NSString *encodedstring = (__bridge NSString *)CFURLCreateStringByAddingPercentEscapes(NULL,
                                                                                               (__bridge CFStringRef)input,
                                                                                               NULL,
                                                                                               (CFStringRef)@"!*'();:@&=+$,/?%#[]",
                                                                                               kCFStringEncodingUTF8);
        return encodedstring;
    }

@end
