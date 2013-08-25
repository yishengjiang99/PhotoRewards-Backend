//
//  UploadViewController.m
//  tapomatic
//
//  Created by Yisheng Jiang on 4/23/13.
//  Copyright (c) 2013 Mobform. All rights reserved.
//

#import "UploadViewController.h"
#import "AppDelegate.h"
#import "Util.h"
#import "DetailedUIViewController.h"


@interface UploadViewController ()

@end

@implementation UploadViewController
@synthesize uploadPicture;
@synthesize category;



- (id)init {
    return [self initWithNibName:@"UploadViewController" bundle:nil];
}

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        UINib *nib = [UINib nibWithNibName:nibNameOrNil bundle:nil];
        [nib instantiateWithOwner:self options:nil];
        self.subtitle.delegate=self;
        [self.subtitle endEditing:YES];
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
}

-(IBAction)editingEnded:(id)sender{
    [sender resignFirstResponder];
}
-(void) uploadImage:(UIImage*)img to:(NSString *)filename type:(NSString *) type
{
    [UIApplication sharedApplication].networkActivityIndicatorVisible = YES;
    

    NSData *imageData = UIImageJPEGRepresentation(img,0);     //change Image to NSData
    
    if (imageData != nil)
    {
        
        NSLog(@"uploading%@", filename);
        
        NSString *urlString = @"http://www.apploot.com/postPicture.php";
        
        NSMutableURLRequest *request = [[NSMutableURLRequest alloc] init] ;
        [request setURL:[NSURL URLWithString:urlString]];
        [request setHTTPMethod:@"POST"];
        
        NSString *boundary = @"---------------------------14737809831466499882746641449";
        NSString *contentType = [NSString stringWithFormat:@"multipart/form-data; boundary=%@",boundary];
        [request addValue:contentType forHTTPHeaderField: @"Content-Type"];
        
        NSMutableData *body = [NSMutableData data];
        [body appendData:[[NSString stringWithFormat:@"\r\n--%@\r\n",boundary] dataUsingEncoding:NSUTF8StringEncoding]];
        [body appendData:[[NSString stringWithFormat:@"Content-Disposition: form-data; name=\"filenames\"\r\n\r\n"] dataUsingEncoding:NSUTF8StringEncoding]];
        [body appendData:[filename dataUsingEncoding:NSUTF8StringEncoding]];
        [body appendData:[[NSString stringWithFormat:@"\r\n--%@\r\n",boundary] dataUsingEncoding:NSUTF8StringEncoding]];
        
        [body appendData:[[NSString stringWithFormat:@"Content-Disposition: form-data; name=\"userfile\"; filename=\"%@\"\r\n", filename]
                          dataUsingEncoding:NSUTF8StringEncoding]];
        [body appendData:[@"Content-Type: application/octet-stream\r\n\r\n" dataUsingEncoding:NSUTF8StringEncoding]];
        [body appendData:[NSData dataWithData:imageData]];
        [body appendData:[[NSString stringWithFormat:@"\r\n--%@--\r\n",boundary] dataUsingEncoding:NSUTF8StringEncoding]];
        [request setHTTPBody:body];
        
        NSData *returnData = [NSURLConnection sendSynchronousRequest:request returningResponse:nil error:nil];
        NSString *returnString = [[NSString alloc] initWithData:returnData encoding:NSUTF8StringEncoding];
        NSLog(@"Response : %@",returnString);
        
        if([returnString isEqualToString:@"1"])
        {
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Success" message:@"Image Saved Successfully and is under review!" delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
            [alert show];
            
            [self.navigationController popViewControllerAnimated:YES];
        }else{
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Failed" message:@"Could not upload to server. Please try again later" delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
            [alert show];
            
        }
        [UIApplication sharedApplication].networkActivityIndicatorVisible = NO;

    }
}
- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload {
    [self setUploadPicture:nil];
    [self setTitle:nil];
    [self setSubtitle:nil];
    [self setTitle:nil];
    [self setSubtitle:nil];
    [super viewDidUnload];
}
- (void)pickerView:(UIPickerView *)pickerView didSelectRow: (NSInteger)row inComponent:(NSInteger)component {
    // Handle the selection
}

// tell the picker how many rows are available for a given component
- (NSInteger)pickerView:(UIPickerView *)pickerView numberOfRowsInComponent:(NSInteger)component {
    NSUInteger numRows = 5;
    
    return numRows;
}

// tell the picker how many components it will have
- (NSInteger)numberOfComponentsInPickerView:(UIPickerView *)pickerView {
    return 1;
}

// tell the picker the title for a given component
- (NSString *)pickerView:(UIPickerView *)pickerView titleForRow:(NSInteger)row forComponent:(NSInteger)component {
    NSString *title;
    title = [@"" stringByAppendingFormat:@"%d",row];
    
    return title;
}

// tell the picker the width of each row for a given component
- (CGFloat)pickerView:(UIPickerView *)pickerView widthForComponent:(NSInteger)component {
    int sectionWidth = 300;
    
    return sectionWidth;
}
- (IBAction)editingExist:(id)sender {
    [sender resignFirstResponder];
}

- (IBAction)upload:(id)sender {
    UIImage *img = self.uploadPicture.image;
    AppDelegate * delegate=[[UIApplication sharedApplication] delegate];
    NSString *macAddress=[delegate getUserId];
    NSString *title = [Util encodedURLParameterString: self.subtitle.text];
    NSString *codedCategory = [Util encodedURLParameterString: self.category];
    NSString *url=[NSString
                   stringWithFormat:@"http://www.apploot.com/uploadPicture.php?mac=%@&category=%@&type=jpeg&title=%@",macAddress,codedCategory,title];
    NSURLRequest *urlRequest = [NSURLRequest requestWithURL:[NSURL URLWithString:url]];
    // Fetch the JSON response
    NSData *urlData;
    NSURLResponse *response;
    NSError *error;
    // Make synchronous request
    urlData = [NSURLConnection sendSynchronousRequest:urlRequest
                                    returningResponse:&response
                                                error:&error];
    
    NSString *filename= [[NSString alloc] initWithData:urlData encoding:NSUTF8StringEncoding];
    
    [UIApplication sharedApplication].networkActivityIndicatorVisible = YES;

    [self uploadImage:img to:filename type:@"jpeg"];
}

- (BOOL)textFieldShouldReturn:(UITextField *)aTextfield {
    NSLog(@"textFieldShouldReturn Fired :)");
    [aTextfield endEditing:YES];
    [aTextfield resignFirstResponder];
    return NO;
}
@end
