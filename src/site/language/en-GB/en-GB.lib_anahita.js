var StringLibAnahita = { 

	action : {
	
		close : 'Close',
		ok : 'Ok',
		save : 'Save',
		cancel : 'Cancel',
		'delete' : 'Delete',
		yes : 'Yes',
		browseImage : 'Browse Image',
		vote : 'Like',
		unvote : 'Unlike',
        openComments: 'Open Comments',
        closeComments: 'Close Comments',
        subscribe: 'Follow',
        unsubscribe: 'Unfollow',
        enable : 'Enable',
        disable : 'Disable'
        
	},
	
	prompt : {
		
		loading : 'Loading...',
		'delete' : 'Are you sure you want to delete this?',	
		deleteActor : 'Are you absolutely sure that you want to delete this profile?',
		inlineEdit : 'Click to edit',
		error : 'Something went wrong!',
		
		username : {
		    
            valid : 'This username is available',
            invalid : 'This username is already taken',
            patternMismatch : 'Username must start with upper or lowercase characters and it may contain numbers in the middle or the end as well as _ or - characters.',
            tooShort : 'Username must be 6 characters or longer',
            tooLong : 'Username is too long'  
		},
		
		email : {
            
            valid : 'This is a good email',
            invalid : 'There is already an account with this email address',
            patternMismatch : 'This is an invalid email format',
            tooShort : 'This email is a little too short',
            tooLong : 'Really? This email is way too long',
            inviteSent : 'Invitation Sent' 
        },
	
	    password : {
            unavailable : 'This is a good email',
            isAvailable : 'There is already an account with this email address',
            patternMismatch : 'This is an invalid email format',
            tooShort : 'This email is a little too short',
            tooLong : 'Really? This email is way too long'  
        },
        
        token : {
            unavailable : 'Sorry, we don\'t seem to have this email in our system!',
            available : 'We have emailed you a link. Click on that link to update your password.'
        }
	}
};