Validation.add('validate-amazonemail', 'Please enter a valid Kindle address. For example johndoe@kindle.com.', function(v) {
    return Validation.get('IsEmpty').test(v) || /^([a-zA-Z0-9]+[a-zA-Z0-9._%-]*@kindle\.com)$/i.test(v)
})