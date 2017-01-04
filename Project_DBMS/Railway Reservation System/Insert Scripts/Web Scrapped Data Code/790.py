import urllib2
import os.path
base_url = 'http://indiarailinfo.com/train/'
for i in range(89400,89500):
	try:
		print '%d\n' % (i)
		filename = str(i)
		if os.path.isfile(filename):
			continue
		url = base_url+filename
		response = urllib2.urlopen(url)
		webContent = response.read()
		f = open(filename,'w')
		f.write(webContent)
		f.close
	except Exception:
		print 'Error on %d ' % (i)
