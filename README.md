# instagram_downloader
A utility that allows the user to download Instagram pictures from shared links
# How it works
> Input: Instagram picture share link

> Output: Raw image download

This PHP page gets in input an Instagram picture share link and let you download the max resolution photo on your PC.
# Workflow

- After the submit of the form the script check if the url is valid
- If it's valid, it goes ahead. If not it gives you an error and go back to the homepage.
- The script read the submitted link's html and it get the useful parts of it (like caption and url of the picture)
- After that it will create a set of headers for downloading the file

# Umanity
Feel free to use or improve this script as you wish
