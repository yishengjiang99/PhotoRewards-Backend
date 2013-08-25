//
//  DetailedUIViewController.m
//  PictureOfTheDay
//
//  Created by Yisheng Jiang on 4/14/13.
//  Copyright (c) 2013 AppLoot. All rights reserved.
//

#import "DetailedUIViewController.h"
#import "UploadViewController.h"
#import "AppDelegate.h"
#import "Util.h"
#import <SDWebImage/UIImageView+WebCache.h>
#import <Twitter/Twitter.h>
@interface DetailedUIViewController ()

@end

@implementation DetailedUIViewController
@synthesize imageView;
@synthesize imgPicker;
@synthesize uploader;
@synthesize category;
@synthesize picList;
@synthesize index;
@synthesize likeBtn;
@synthesize likeCount;
- (id)init {
    return [self initWithNibName:@"DetailedUIViewController" bundle:nil];
}

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        self.view = [[UIView alloc] initWithFrame:[UIScreen mainScreen].applicationFrame];
        self.view.backgroundColor=[UIColor blackColor];
        self.imageView.backgroundColor=[UIColor blackColor];
        UINib *nib = [UINib nibWithNibName:nibNameOrNil bundle:nil];
        [nib instantiateWithOwner:self options:nil];
        self.navigationController.navigationBar.alpha = .3;
        
        UISwipeGestureRecognizer* swipeLeftGestureRecognizer = [[UISwipeGestureRecognizer alloc] initWithTarget:self action:@selector(handleSwipeLeftFrom:)];
        swipeLeftGestureRecognizer.direction = UISwipeGestureRecognizerDirectionLeft;
        
        UISwipeGestureRecognizer* swipeRightGestureRecognizer = [[UISwipeGestureRecognizer alloc] initWithTarget:self action:@selector(handleSwipeRightFrom:)];
        
        swipeRightGestureRecognizer.direction = UISwipeGestureRecognizerDirectionRight;
        [self.view addGestureRecognizer:swipeLeftGestureRecognizer];
        [self.view addGestureRecognizer:swipeRightGestureRecognizer];

        self.imageView.contentMode = UIViewContentModeScaleAspectFit;
        self.uploader=[[UploadViewController alloc] init];
        self.imgPicker = [[UIImagePickerController alloc] init];
        self.index=0;
        self.imgPicker.delegate = self;
        
        // Custom initialization
    }
    return self;
}
- (void)handleSwipeRightFrom:(UIGestureRecognizer*)recognizer {
    if(self.index==0){
        self.index=[self.picList count]-1;
    }else{
        self.index--;
    }
    [self loadPictureByIndex];
}
- (void)handleSwipeLeftFrom:(UIGestureRecognizer*)recognizer {
    if(self.index>=[self.picList count]-1){
        self.index=0;
    }else{
        self.index++;
    }
    [self loadPictureByIndex];
}
-(void) loadPictureByIndex
{
    NSDictionary *picture = [self.picList objectAtIndex:self.index];
    NSString* url = (NSString *)[picture objectForKey:@"url"];
    
    NSString *title=(NSString *)[picture objectForKey:@"title"];
    self.likeCount=[[picture objectForKey:@"liked"] integerValue];
    self.banner.text=[NSString stringWithFormat:@"%@ (%i likes)", title,likeCount];
    [self updateLikes];
    [self.imageView setImageWithURL:[NSURL URLWithString:url]
                   placeholderImage:[UIImage imageNamed:@"placeholder.png"]
                            options:SDWebImageRefreshCached];
    
//    UIImage *image = [UIImage imageWithData:[NSData dataWithContentsOfURL:[NSURL URLWithString:url]]];
//    self.imageView.image=image;
}
-(void) updateLikes
{
    NSDictionary *picture = [self.picList objectAtIndex:self.index];
    NSString *title=(NSString *)[picture objectForKey:@"title"];

    self.banner.text=[NSString stringWithFormat:@"%@ (%i likes)", title,likeCount];
}
- (void)viewDidLoad
{
    [super viewDidLoad];
    self.navigationController.toolbarHidden = NO;

    // Do any additional setup after loading the view from its nib.
}

- (void)imagePickerController:(UIImagePickerController *)picker didFinishPickingImage:(UIImage *)img editingInfo:(NSDictionary *)editInfo {
   // NSString *mediaType = [editInfo objectForKey: UIImagePickerControllerMediaType];
    
    [self.imgPicker dismissModalViewControllerAnimated:NO];
    self.uploader.uploadPicture.contentMode = UIViewContentModeScaleAspectFit;

    self.uploader.uploadPicture.image=img;
    self.uploader.category=self.category;
    [self.navigationController pushViewController:self.uploader animated:YES];    
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (IBAction)uploadBtn:(id)sender {
    self.imgPicker.sourceType = UIImagePickerControllerSourceTypePhotoLibrary;
    [self presentModalViewController:self.imgPicker animated:YES];
    
    NSLog(@"uploading");
}

- (IBAction)cameraClicked:(id)sender {
    self.imgPicker.sourceType = UIImagePickerControllerSourceTypeCamera;
    [self presentModalViewController:self.imgPicker animated:YES];
    NSLog(@"uploading");
}

- (IBAction)shareClicked:(id)sender {
    NSDictionary *picture = [self.picList objectAtIndex:self.index];
    
    TWTweetComposeViewController *tweet = [[TWTweetComposeViewController alloc] init];
    NSString *text=[NSString stringWithFormat:@"Picture of The Day from #Tapomatic: %@",[picture objectForKey:@"title"]];
    [tweet setInitialText:text];
    [tweet addImage:self.imageView.image];
    [self presentModalViewController:tweet animated:YES];
    

}

- (IBAction)likeClicked:(id)sender {
    AppDelegate * delegate=[[UIApplication sharedApplication] delegate];
    NSString *macAddress=[delegate getUserId];
    NSDictionary *picture = [self.picList objectAtIndex:self.index];
    NSString *picId=[picture objectForKey:@"id"];
    NSString *url=[NSString
                   stringWithFormat:@"http://www.apploot.com/like_picture.php?uid=%@&picid=%@",macAddress,picId];
    NSString *ret=[Util httpget:url];
    if([ret isEqualToString:@"1"]){
        self.likeCount++;
        [self updateLikes];
    }else{
        UIAlertView *alert = [[UIAlertView alloc]
                              initWithTitle: @""
                              message: @"You already liked this picture"
                              delegate: nil
                              cancelButtonTitle:@"Ok"
                              otherButtonTitles:nil, nil];
        [alert show];

    }
}
- (void)viewDidUnload {
    [self setLikeBtn:nil];
    [self setCategory:nil];
    [self setView:nil];
    [self setLikeBtn:nil];
    [self setImgPicker:nil];
    [self setBanner:nil];
    [super viewDidUnload];
}
@end
