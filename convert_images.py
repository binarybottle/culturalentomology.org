#!/usr/bin/env python
"""
Use ImageMagick's mogrify command to resize and reformat image files.

2016 . Arno Klein (arno@binarybottle.com) . Apache v2.0 License
"""
import os
import sys
import shutil
from subprocess import call

submitted_files_path = '/home/pupating/culturalentomology.org/submissions'
moved_nonimages_path = '/home/pupating/culturalentomology.org/submitted_nonimages';
converted_images_path = '/home/pupating/culturalentomology.org/submitted_images'
image_extensions = ['bmp','gif','jpg','jpeg','pjpeg','png','tif','tiff',
                    'BMP','GIF','JPG','JPEG','PJPEG','PNG','TIF','TIFF']
new_extension = 'jpg'
resize_dims = '1200x' #'800x'
quality = '100'
cmd = 'convert'

submitted_files = os.listdir(submitted_files_path)
converted_images = os.listdir(converted_images_path)

# Loop through raw files:
for submitted_file in submitted_files:

    input_file_no_quotes = '{0}'.format(os.path.join(submitted_files_path,
                                           submitted_file))
    input_file = '"{0}"'.format(input_file_no_quotes)

    # If raw file has an acceptable image file extension:
    exploded_filename = submitted_file.split('.')
    filestem = '.'.join(exploded_filename[:-1])
    extension = exploded_filename[-1]
    if extension in image_extensions:

        # If the file has not already been converted:
        converted_file = '.'.join([filestem, new_extension])
        if converted_file not in converted_images:

            output_file = '"{0}"'.format(os.path.join(converted_images_path,
                                                      converted_file))
            arg_resize = " -adaptive-resize " + resize_dims
            args = [cmd, input_file, arg_resize, output_file]
            command = " ".join(args)
            print(command)
            try:
                retcode = call(command, shell=True)
                if retcode < 0:
                    print("Child was terminated by signal", -retcode)
                elif retcode == 0:
                    pass
                else:
                    print("Child returned {0}".format(retcode))
            except OSError as e:
                print("Execution failed: {0}".format(e))

            # Update list; if .tif file converted to FILE-0.jpg instead of FILE.jpg:
            converted_file0 = '.'.join([filestem + '-0', new_extension])
            output_file0 = os.path.join(converted_images_path, converted_file0)
            converted_images = os.listdir(converted_images_path)
            if converted_file not in converted_images and converted_file0 in converted_images:

                # Change permissions:
                command3 = "chmod 755 " + output_file0
                print(command3)
                try:
                    retcode = call(command3, shell=True)
                    if retcode < 0:
                        print("Child was terminated by signal", -retcode)
                    elif retcode == 0:
                        pass
                    else:
                        print("Child returned {0}".format(retcode))
                except OSError as e:
                    print("Execution failed: {0}".format(e))
                
                command4 = "mv " + output_file0 + " " + output_file
                print(command4)
                try:
                    retcode = call(command4, shell=True)
                    if retcode < 0:
                        print("Child was terminated by signal", -retcode)
                    elif retcode == 0:
                        pass
                    else:
                        print("Child returned {0}".format(retcode))
                except OSError as e:
                    print("Execution failed: {0}".format(e))

            # Change permissions:
            command5 = "chmod 755 " + output_file
            print(command5)
            try:
                retcode = call(command5, shell=True)
                if retcode < 0:
                    print("Child was terminated by signal", -retcode)
                elif retcode == 0:
                    pass
                else:
                    print("Child returned {0}".format(retcode))
            except OSError as e:
                print("Execution failed: {0}".format(e))

    # Non-image files:
    else:
        moved_file = os.path.join(moved_nonimages_path, submitted_file)
        print(input_file_no_quotes)
        print(moved_file)
        shutil.move(input_file_no_quotes, moved_file)

        # Change permissions:
        command6 = "chmod 755 " + moved_file
        print(command6)
        try:
            retcode = call(command6, shell=True)
            if retcode < 0:
                print("Child was terminated by signal", -retcode)
            elif retcode == 0:
                pass
            else:
                print("Child returned {0}".format(retcode))
        except OSError as e:
            print("Execution failed: {0}".format(e))

