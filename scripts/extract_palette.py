from colorthief import ColorThief
import requests
from io import BytesIO

url = 'https://www.avss-asso.fr/wp-content/uploads/2023/12/Logo-AVSS78-mini-1-250x250.png'
resp = requests.get(url)
img = BytesIO(resp.content)
ct = ColorThief(img)
palette = ct.get_palette(color_count=6)
print(["#{:02x}{:02x}{:02x}".format(*c) for c in palette])
