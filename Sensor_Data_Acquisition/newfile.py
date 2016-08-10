
req_obj = request.body.read()
devCode = req_obj.split('"')[1]
value = req_obj.split('\n')[3]
print "devCode=" + str(devCode) + "&value=" + str(value)
