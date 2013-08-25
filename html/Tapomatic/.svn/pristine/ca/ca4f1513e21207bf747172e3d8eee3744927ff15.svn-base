//
//  DetailedUIViewController.h
//  PictureOfTheDay
//
//  Created by Yisheng Jiang on 4/14/13.
//  Copyright (c) 2013 AppLoot. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "UploadViewController.h"
#import <SDWebImage/UIImageView+WebCache.h>

@interface DetailedUIViewController : UIViewController <UINavigationControllerDelegate,UIImagePickerControllerDelegate>
@property (weak, nonatomic) IBOutlet UIImageView *imageView;
@property (nonatomic, retain) UploadViewController *uploader;
@property (weak, nonatomic) IBOutlet UILabel *banner;
- (IBAction)uploadBtn:(id)sender;
- (IBAction)cameraClicked:(id)sender;
- (IBAction)shareClicked:(id)sender;
- (IBAction)likeClicked:(id)sender;
@property (weak, nonatomic) IBOutlet UIBarButtonItem *likeBtn;
@property (weak, nonatomic) NSString *category;
@property (nonatomic, retain) UIImagePickerController *imgPicker;
@property (strong, nonatomic) NSArray *picList;
@property (assign, nonatomic) NSInteger index;
@property (assign, nonatomic) NSInteger likeCount;
-(void) loadPictureByIndex;
@end
