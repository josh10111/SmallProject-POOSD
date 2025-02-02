const urlBase = 'http://138.197.77.191/LAMPAPI';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";



function doLogin(event)
{
	event.preventDefault();

	userId = 0;
	firstName = "";
	lastName = "";
	
	let login = document.getElementById("loginName").value;
	let password = document.getElementById("loginPassword").value;

	console.log("Login:", login);
	console.log("password:", password);

	// Easter egg check
	if (login === "RICK-LEINECKER" && password === "scuba2havefun") {
		// top easter egg
		document.getElementById("easterEggImage-top").classList.remove("hidden");
		document.getElementById("easterEggImage-top").classList.add("spin");

		// bottom easter egg
		document.getElementById("easterEggImage-bottom").classList.remove("hidden");
		document.getElementById("easterEggImage-bottom").classList.add("spin");

		
		// right easter egg
		document.getElementById("easterEggImage-right").classList.remove("hidden");
		document.getElementById("easterEggImage-right").classList.add("swim");

		
		// left easter egg
		document.getElementById("easterEggImage-left").classList.remove("hidden");
		document.getElementById("easterEggImage-left").classList.add("swim");

		
		// hide status
		document.getElementById("errorStatus").classList.add("hidden");
		document.getElementById("loginStatus").classList.add("hidden");
		
		return; 
	}
	
	let url = urlBase + '/Login.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var response = JSON.parse(xhr.responseText);

				if (response.error) {
					// show error status
					document.getElementById("errorStatus").classList.remove("hidden");
					document.getElementById("errorMsgBox").innerHTML = response.error;

					// Hide the error box after 3 seconds
					setTimeout(function() {
						document.getElementById("errorStatus").classList.add("hidden");
					}, 5000);

				} else {
					
					// set userId (userId is sent as "id" from server), firstName, lastName
					userId = response.id;
					firstName = response.firstName;
					lastName = response.lastName;
					
					// save cookies
					saveCookie();

					// show login status
                    document.getElementById("loginStatus").classList.remove("hidden");
                    // redirect to contacts.html page after a short delay
                    setTimeout(function() {
                        window.location.href = "contacts.html";
                    }, 1000);
				}
			} 
		};
		var data = JSON.stringify({
			login: login, 
			password: password
		});

		console.log("Sending data:", data);

		try {
			xhr.send(data);
		} catch (e) {
			console.error("XHR send error:", e);
		}
	}
	catch(err)
	{
		alert(err.message);
	}

}

function saveCookie()
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "firstName=" + firstName + ";expires=" + date.toGMTString() + ";path=/";
	document.cookie = "lastName=" + lastName + ";expires=" + date.toGMTString() + ";path=/";
	document.cookie = "userId=" + userId + ";expires=" + date.toGMTString() + ";path=/";
}

function readCookie()
{
	userId = -1;
	let data = document.cookie;
	let splits = data.split(";");
	for(var i = 0; i < splits.length; i++) 
	{
		let thisOne = splits[i].trim();
		let tokens = thisOne.split("=");
		console.log("Cookie:", tokens[0]);
		console.log("Value:", tokens[1]);
		if( tokens[0] == "firstName" )
		{
			firstName = tokens[1];
		}
		else if( tokens[0] == "lastName" )
		{
			lastName = tokens[1];
		}
		else if( tokens[0] == "userId" )
		{
			userId = parseInt( tokens[1].trim() );
		}
	}
	
	if( userId < 0 )
	{
		window.location.href = "index.html";
	}
	else
	{
		document.getElementById("userName").innerHTML = "Logged in as: " + firstName + " " + lastName;
		document.getElementById("userIdDisplay").innerHTML = "User ID:" + userId;

	}
}

function doLogout()
{
	userId = 0;
	firstName = "";
	lastName = "";
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}

function addColor()
{
	let newColor = document.getElementById("colorText").value;
	document.getElementById("colorAddResult").innerHTML = "";

	let tmp = {color:newColor,userId,userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/AddColor.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("colorAddResult").innerHTML = "Color has been added";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("colorAddResult").innerHTML = err.message;
	}
	
}

function searchColor()
{
	let srch = document.getElementById("searchText").value;
	document.getElementById("colorSearchResult").innerHTML = "";
	
	let colorList = "";

	let tmp = {search:srch,userId:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/SearchColors.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("colorSearchResult").innerHTML = "Color(s) has been retrieved";
				let jsonObject = JSON.parse( xhr.responseText );
				
				for( let i=0; i<jsonObject.results.length; i++ )
				{
					colorList += jsonObject.results[i];
					if( i < jsonObject.results.length - 1 )
					{
						colorList += "<br />\r\n";
					}
				}
				
				document.getElementsByTagName("p")[0].innerHTML = colorList;
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("colorSearchResult").innerHTML = err.message;
	}
	
}

function validRegistration() {
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirmPassword").value;
    var firstName = document.getElementById("firstName").value;
    var lastName = document.getElementById("lastName").value;

    console.log("Got to validRegistration");

    if (username == "" || password == "" || confirmPassword == "" || firstName == "" || lastName == "") {
        alert("Please fill out all fields.");
        return false;
    }

    if (password != confirmPassword) {
        alert("Passwords do not match.");
        return false;
    }

    return true;
}

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("registerForm").onsubmit = function(event) {
        event.preventDefault(); // Prevent the default form submission
        if (validRegistration()) {
            doRegister();
        }
	};
});

function doRegister()
{
	var firstName = document.getElementById("firstName").value;
	var lastName = document.getElementById("lastName").value;
	var username = document.getElementById("username").value;
	var password = document.getElementById("password").value;

	console.log("First Name:", firstName);
    console.log("Last Name:", lastName);
    console.log("Username:", username);
    console.log("Password:", password);

	var xhr = new XMLHttpRequest();
	var url = urlBase + '/Register.' + extension;
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try {
		xhr.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var response = JSON.parse(xhr.responseText);
				if (response.error) {
					alert(response.error);
				} else {
					alert("Registration successful.");
					showLogin();
				}
			} else {
				console.error("Error during registration:", this.statusText);
			}
		};
	
		var data = JSON.stringify({
			firstName: firstName, 
			lastName: lastName, 
			login: username, 
			password: password
		});

		console.log("Sending data:", data);

		try {
			xhr.send(data);
		} catch (e) {
			console.error("XHR send error:", e);
		}
	}
	catch(err) {
		alert(err.message);
	}
}
