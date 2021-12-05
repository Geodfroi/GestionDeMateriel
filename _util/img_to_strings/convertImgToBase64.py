#! python3
"""This scripts is meant to convert an img to a ASCII string. The string can then be inserted inside an img tag 
<img src="data:image/jpeg;charset=utf-8;{{ASCIISTR}}" alt="Description of the image" directly inside an html file.
Images can then be displayed in the downloaded html file for students/>
"""

################################
## Joël Piguet - 2021.12.05 ###
##############################


# https://appdividend.com/2020/06/23/how-to-convert-image-to-base64-string-in-python
import base64
import os
from pathlib import Path
import pyperclip  # type: ignore

# change working directory to the directory containing this file
# os.chdir(os.path.dirname(os.path.abspath(__file__)))
os.chdir(Path(__file__).parent)

img_path = Path("logo.jpg")
tag = '<img src="data:image/jpeg;charset=utf-8;base64,{{DATA}}" alt="Description of the image" />'

with open(img_path, "rb") as img_f:
    b64_string = base64.b64encode(img_f.read())

print(b64_string)

with open("img_conv_result.txt", "w") as txt_f:
    str = b64_string.decode("utf-8")
    tag = tag.replace("{{DATA}}", str)
    pyperclip.copy(tag)  # copy to clipboard ready to insert inside html
    txt_f.write(tag)
