#!/usr/bin/env python
"""
Use ImageMagick's convert command to resize and reformat image files.
See https://www.imagemagick.org/script/command-line-processing.php

2016-2017 . Arno Klein (arno@binarybottle.com) . Apache v2.0 License
"""
import os
import sys
import shutil
from subprocess import call

#-----------------------------------------------------------------------------------
# Set up paths:
#-----------------------------------------------------------------------------------
submissions_path = '/home/pupating/culturalentomology.org/submitted'
nonimages_path = '/home/pupating/culturalentomology.org/submitted_nonimages';
images_path = '/home/pupating/culturalentomology.org/submitted_converted_images'
resized_images_path = '/home/pupating/culturalentomology.org/submitted_converted_resized_images'

#-----------------------------------------------------------------------------------
# Image settings:
#-----------------------------------------------------------------------------------
image_extensions = ['bmp','gif','jpg','jpeg','pjpeg','png','tif','tiff',
                    'BMP','GIF','JPG','JPEG','PJPEG','PNG','TIF','TIFF']
new_extension = 'jpg'
resize_heights = [5000, 4750, 4500, 4250, 4000, 3750, 3500, 3250, 3000, 2750, 2500,
                  2250, 2000, 1750, 1500, 1250, 1000, 800]  # resize in stages
quality = '100'
cmd = 'convert'

submitted_files = os.listdir(submissions_path)
converted_images = os.listdir(images_path)
converted_resized_images = os.listdir(resized_images_path)

#-----------------------------------------------------------------------------------
# Try command function:
#-----------------------------------------------------------------------------------
def try_command(command, verbose=True):
    try:
        if verbose:
            print(command)
        retcode = call(command, shell=True)
        if retcode < 0:
            print("Child was terminated by signal", -retcode)
        elif retcode == 0:
            pass
        else:
            print("Child returned {0}".format(retcode))
    except OSError as e:
        print("Execution failed: {0}".format(e))

#-----------------------------------------------------------------------------------
# Run convert image function:
#-----------------------------------------------------------------------------------
def run_convert_image(input_file, resize_dims, output_file):
    if resize_dims:
        arg_resize = " -adaptive-resize " + resize_dims
    else:
        arg_resize = " "
    args = ['convert -flatten', input_file, arg_resize, output_file]
    try_command(" ".join(args))

    # Change permissions:
    try_command("chmod 755 " + output_file)

#-----------------------------------------------------------------------------------
# Function to remove files from list of zero size:
#-----------------------------------------------------------------------------------
def store_nonzero_files(file_list, file_path):
    new_list = []
    for file_in_list in file_list:
        full_path = os.path.join(file_path, file_in_list)
        statinfo = os.stat(full_path)
        if statinfo.st_size > 0:
            new_list.append(file_in_list)
    return new_list

#-----------------------------------------------------------------------------------
# Loop through raw files:
#-----------------------------------------------------------------------------------
for submitted_file in submitted_files:

    input_file_no_quotes = '{0}'.format(os.path.join(submissions_path,
                                        submitted_file))
    input_file = '"{0}"'.format(input_file_no_quotes)

    # If submitted file has an acceptable image file extension:
    exploded_filename = submitted_file.split('.')
    filestem = '.'.join(exploded_filename[:-1])
    extension = exploded_filename[-1]
    if extension in image_extensions:
        converted_file = '.'.join([filestem, new_extension])

        # If the file has not already been reformatted (at original size):
        if converted_file not in converted_images:

            output_file = '"{0}"'.format(os.path.join(images_path,
                                                      converted_file))
            # Reformat (without resizing):
            run_convert_image(input_file, '', output_file)

            # Keep only those converted images with size greater than zero:
            converted_images = store_nonzero_files(converted_images,
                                                   images_path)

        # If the file has not already been reformatted and resized:
        if converted_file not in converted_resized_images:

            input_file = '"{0}"'.format(os.path.join(images_path, converted_file))
            copy_file = '"{0}"'.format(os.path.join(resized_images_path,
                                                    converted_file))
            # Copy:
            args = ['cp', input_file, copy_file]
            try_command(" ".join(args))
            try_command("chmod 755 " + copy_file)

            # Loop through heights and shrink only if bigger than supplied dimensions:
            for height in resize_heights:
                resize_dims = '>{0}x'.format(height)
                run_convert_image(copy_file, resize_dims, copy_file)

             # Keep only those converted images with size greater than zero:
             converted_resized_images = store_nonzero_files(converted_resized_images,
                                                            resized_images_path)
                
    # Non-image files:
    else:
        moved_file = os.path.join(nonimages_path, submitted_file)
        shutil.move(input_file_no_quotes, moved_file)
        try_command("chmod 755 " + moved_file)

