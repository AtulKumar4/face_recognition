"""
@author: AKumar44
"""
#import required libraries 
import cv2
#import os module for reading training data directories and paths
import os
#import numpy to convert python lists to numpy arrays as 
#it is needed by OpenCV face recognizers
import numpy as np
#from sklearn import preprocessing
#le = preprocessing.LabelEncoder()
from flask import Flask
from PIL import Image
#import pytesseract
#import re
import shutil
import glob
import pickle
from flask import send_file,request,abort
#import json
from imutils import paths
import face_recognition
#import argparse
import os
#from sklearn.preprocessing import LabelEncoder

app = Flask(__name__)
global subjects                    
@app.route('/', methods=['GET', 'POST'])
def upload_file():
    #with open("label.txt", "rb") as fp:   # Unpickling
        #subjects = pickle.load(fp)
    #fp.close()    
    if request.method == 'POST':
       print("in trainging") 
       
       data = request.get_json(force=True)
       names = data.get('name') 
       print(names)       
       
       def prepare_training_data(data_folder_path):
            srcpath = "trainer"
            destpath = "training-data"
            
            
            for root, subFolders, files in os.walk(srcpath):
                for file in files:
                    #end = file.find('_')
                    folder = names
            
                    subFolder = os.path.join(destpath, folder)
                    if not os.path.isdir(subFolder):
                        os.makedirs(subFolder)
                    shutil.move(os.path.join(root, file), subFolder)
                    
            imagePaths = list(paths.list_images(destpath))

            # initialize the list of known encodings and known names
            knownEncodings = []
            knownNames = []
            
            # loop over the image paths
            for (i, imagePath) in enumerate(imagePaths):
            	# extract the person name from the image path
            	print("[INFO] processing image {}/{}".format(i + 1,
            		len(imagePaths)))
            	name = imagePath.split(os.path.sep)[-2]
            
            	# load the input image and convert it from RGB (OpenCV ordering)
            	# to dlib ordering (RGB)
            	image = cv2.imread(imagePath)
            	rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
            
            	# detect the (x, y)-coordinates of the bounding boxes
            	# corresponding to each face in the input image
            	boxes = face_recognition.face_locations(rgb,
            		model="cnn")
            
            	# compute the facial embedding for the face
            	encodings = face_recognition.face_encodings(rgb, boxes)
            
            	# loop over the encodings
            	for encoding in encodings:
            		# add each encoding + name to our set of known names and
            		# encodings
            		knownEncodings.append(encoding)
            		knownNames.append(name)
            
            # dump the facial encodings + names to disk
            print("[INFO] serializing encodings...")
            data = {"encodings": knownEncodings, "names": knownNames}
            f = open("encodings.pickle", "wb")
            f.write(pickle.dumps(data))
            f.close()        
            
       print("Preparing data...")
       prepare_training_data("training-data")
       print("Data prepared")
        
       return ''
       
    else:
        print("in non training ")
        
        def predict(test_img):
            
            image = test_img.copy()
            print("[INFO] loading encodings...")
            data = pickle.loads(open("encodings.pickle", "rb").read())
            
            # load the input image and convert it from BGR to RGB
            #image = cv2.imread(img)
            rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
            
            # detect the (x, y)-coordinates of the bounding boxes corresponding
            # to each face in the input image, then compute the facial embeddings
            # for each face
            print("[INFO] recognizing faces...")
            boxes = face_recognition.face_locations(rgb,
            	model="cnn")
            encodings = face_recognition.face_encodings(rgb, boxes)
            
            # initialize the list of names for each face detected
            names = []
            
            # loop over the facial embeddings
            for encoding in encodings:
            	# attempt to match each face in the input image to our known
            	# encodings
            	matches = face_recognition.compare_faces(data["encodings"],
            		encoding)
            	name = "Unknown"
            
            	# check to see if we have found a match
            	if True in matches:
            		# find the indexes of all matched faces then initialize a
            		# dictionary to count the total number of times each face
            		# was matched
            		matchedIdxs = [i for (i, b) in enumerate(matches) if b]
            		counts = {}
            
            		# loop over the matched indexes and maintain a count for
            		# each recognized face face
            		for i in matchedIdxs:
            			name = data["names"][i]
            			counts[name] = counts.get(name, 0) + 1
            
            		# determine the recognized face with the largest number of
            		# votes (note: in the event of an unlikely tie Python will
            		# select first entry in the dictionary)
            		name = max(counts, key=counts.get)
            	
            	# update the list of names
            	names.append(name)
            
            # loop over the recognized faces
            for ((top, right, bottom, left), name) in zip(boxes, names):
            	# draw the predicted face name on the image
            	cv2.rectangle(image, (left, top), (right, bottom), (0, 255, 0), 2)
            	y = top - 15 if top - 15 > 15 else top + 15
            	cv2.putText(image, name, (left, y), cv2.FONT_HERSHEY_SIMPLEX,
            		0.75, (0, 255, 0), 2)
            
            return image   
        
        print("Predicting images...")
        for infile in glob.glob('C:\\wamp64\\www\\captureImage\\upload\\*.jpeg'):
            input_file = infile
            print(infile)

        test_img3 = cv2.imread(input_file)

        predicted_img3 = predict(test_img3)
        
        print("Prediction complete")
        cv2.imwrite('C:/wamp64/www/captureImage/output/output.jpeg', predicted_img3)
        for f in os.listdir(r'C:/wamp64/www/captureImage/upload'):
            if f.endswith('.jpeg'):
                print("removing part")
                os.remove(input_file)

        return ''
#cv2.destroyAllWindows()

if __name__ == '__main__':
     app.run(port=5000)
     
