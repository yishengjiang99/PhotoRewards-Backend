//
//  FriendsViewController.m
//  fbtracker
//
//  Created by Yisheng Jiang on 4/10/13.
//  Copyright (c) 2013 Yisheng Jiang. All rights reserved.
//

#import "FriendsViewController.h"
#import <FacebookSDK/FacebookSDK.h>

@interface FriendsViewController ()
@end

@implementation FriendsViewController
@synthesize data =_data;
@synthesize module;
@synthesize toolbar = _toolbar;
- (id)initWithStyle:(UITableViewStyle)style
{
    self = [super initWithStyle:style];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Add a toolbar to hold a Done button that will dismiss this view controller
    self.toolbar = [[UIToolbar alloc] init];
    self.toolbar.barStyle = UIBarStyleDefault;
    [self.toolbar setAutoresizingMask:UIViewAutoresizingFlexibleWidth];
    [self.toolbar sizeToFit];
    
    UIBarButtonItem *doneButton = [[UIBarButtonItem alloc]
                                   initWithBarButtonSystemItem:UIBarButtonSystemItemDone
                                   target:self
                                   action:@selector(doneButtonPressed:)];
    
    UIBarButtonItem *space = [[UIBarButtonItem alloc]
                              initWithBarButtonSystemItem:UIBarButtonSystemItemFlexibleSpace
                              target:nil
                              action:nil];
    
    self.toolbar.items = [NSArray arrayWithObjects:space, doneButton, nil];
     
    // Uncomment the following line to preserve selection between presentations.
    // self.clearsSelectionOnViewWillAppear = NO;
 
    // Uncomment the following line to display an Edit button in the navigation bar for this view controller.
    // self.navigationItem.rightBarButtonItem = self.editButtonItem;
}



- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{

    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
      return [self.data count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    
    static NSString *CellIdentifier = @"Friend";
    UITableViewCell *cell =
    [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    
    if (cell == nil) {
        cell = [[UITableViewCell alloc]
                initWithStyle:UITableViewCellStyleSubtitle
                reuseIdentifier:CellIdentifier];
        [cell setAccessoryType:UITableViewCellAccessoryDisclosureIndicator];
    }

    cell.textLabel.text = [[self.data objectAtIndex:indexPath.row]
                           objectForKey:@"name"];
    if([self.module isEqualToString: @"popular"]){
        NSDecimalNumber *friends = [[self.data objectAtIndex:indexPath.row]
                                    objectForKey:@"friend_count"];
        cell.detailTextLabel.text=[NSString stringWithFormat:@"%@ friends",friends];
    }else if([self.module isEqualToString: @"active"]){
        NSDecimalNumber *likes = [[self.data objectAtIndex:indexPath.row]
                                    objectForKey:@"likes_count"];
        cell.detailTextLabel.text=[NSString stringWithFormat:@"%@ pages liked",likes];
    }
    UIImage *image = [UIImage imageWithData:
                      [NSData dataWithContentsOfURL:
                       [NSURL URLWithString:
                        [[self.data objectAtIndex:indexPath.row]
                         objectForKey:@"pic_square"]]]];
    
    cell.imageView.image = image;
    
    return cell;
    // Configure the cell...
    
}


- (BOOL)tableView:(UITableView *)tableView canEditRowAtIndexPath:(NSIndexPath *)indexPath
{
    // Return NO if you do not want the specified item to be editable.
    return YES;
}


// Override to support editing the table view.
- (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath
{
    if (editingStyle == UITableViewCellEditingStyleDelete) {
        // Delete the row from the data source
        [tableView deleteRowsAtIndexPaths:@[indexPath] withRowAnimation:UITableViewRowAnimationFade];
    }   
    else if (editingStyle == UITableViewCellEditingStyleInsert) {
        // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view
    }   
}


/*
// Override to support rearranging the table view.
- (void)tableView:(UITableView *)tableView moveRowAtIndexPath:(NSIndexPath *)fromIndexPath toIndexPath:(NSIndexPath *)toIndexPath
{
}
*/

/*
// Override to support conditional rearranging of the table view.
- (BOOL)tableView:(UITableView *)tableView canMoveRowAtIndexPath:(NSIndexPath *)indexPath
{
    // Return NO if you do not want the item to be re-orderable.
    return YES;
}
*/
- (void)doneButtonPressed:(id)sender
{
    // Dismiss view controller based on supported methods
    if ([self respondsToSelector:@selector(presentingViewController)]) {
        // iOS 5+ support
        [[self presentingViewController] dismissModalViewControllerAnimated:YES];
    } else {
        [[self parentViewController] dismissModalViewControllerAnimated:YES];
        
    }
}
#pragma mark - Table view delegate
- (UIView *)tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section
{
    return self.toolbar;
}

- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section
{
    return self.toolbar.frame.size.height;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    
}

@end
