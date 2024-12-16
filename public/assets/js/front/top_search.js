var countries = ['Help','Account Details','Feedback','Change Password','Fund Transfer','Payments','Home','Overview',"Deposit","Bank Agent List","Private Banking","Safe Bank","My Digital Asset","Buy Airtime","Compliance","Dashboard","Add Funds","Transfer","Withdrawal","Buy/Sell Crypto","Transaction History","Agents List","Profile","Exchange","Become a Bank Agent","Affiliate Program","Notifications"];
function goTo(page_name)
{
  //alert(page_name);	
  if (page_name == "Help") {
	location.href = 'https://www.nimbleappgenie.live/dafri/auth/help';  
  }
  else if (page_name == "Feedback") {
	location.href = 'https://www.nimbleappgenie.live/dafri/auth/feedback';  
  }
  else if (page_name == "Account Details") {
	location.href = 'https://nimbleappgenie.live/dafri/auth/account-detail';  
  }
  else if (page_name == "Change Password") {
	location.href = 'https://www.nimbleappgenie.live/dafri/auth/change-pin';  
  }
  else if (page_name == "Fund Transfer") {
	location.href = 'https://www.nimbleappgenie.live/dafri/auth/fund-transfer';  
  }
  else if (page_name == "Payments") {
	location.href = 'https://www.nimbleappgenie.live/dafri/auth/transactions';  
  }
  else if (page_name == "Home") {
	location.href = 'https://nimbleappgenie.live/dafri/overview';  
  }
  else if (page_name == "Overview") {
	location.href = 'https://nimbleappgenie.live/dafri/overview';  
  }
  else if (page_name == "Deposit") {
	location.href = 'https://nimbleappgenie.live/dafri/auth/add-fund';  
  }
  else if (page_name == "Bank Agent List") {
	location.href = 'https://www.nimbleappgenie.live/dafri/auth/agent-list';  
  }
  else if (page_name == "Private Banking") {
	location.href = 'https://www.nimbleappgenie.live/dafri/auth/private-banking';  
  }
  else if (page_name == "Safe Bank") {
	location.href = 'https://coinmarketcap.com/currencies/safebank-yes/';  
  }
  else if (page_name == "My Digital Asset") {
	location.href = 'https://nimbleappgenie.live/dafri/auth/comming-soon';  
  }
  else if (page_name == "Buy Airtime") {
	location.href = 'https://nimbleappgenie.live/dafri/auth/comming-soon';  
  }
  else if (page_name == "Compliance") {
	location.href = 'https://nimbleappgenie.live/dafri/auth/compliance';  
  }
  else if (page_name == "Add Funds") {
	location.href = 'https://nimbleappgenie.live/dafri/auth/add-fund';  
  }
  else if (page_name == "Transfer") {
	location.href = 'https://nimbleappgenie.live/dafri/auth/fund-transfer';  
  }
  else if (page_name == "Withdrawal") {
	location.href = 'https://nimbleappgenie.live/dafri/auth/withdraw-request';  
  }
  else if (page_name == "Buy/Sell Crypto") {
	location.href = 'https://www.nimbleappgenie.live/dafri/buy-cell-crypto';  
  }
  else if (page_name == "Payments") {
	location.href = 'https://nimbleappgenie.live/dafri/auth/transactions';  
  }
  else if (page_name == "Transaction History") {
	location.href = 'https://nimbleappgenie.live/dafri/auth/transactions';  
  }
  else if (page_name == "Agents List") {
	location.href = 'https://nimbleappgenie.live/dafri/auth/agent-list';  
  }
  else if (page_name == "Profile") {
	location.href = 'https://nimbleappgenie.live/dafri/auth/account-detail';  
  }
  else if (page_name == "Exchange") {
	location.href = 'https://www.nimbleappgenie.live/dafri/dafrixchange';  
  }
  else if (page_name == "Become a Bank Agent") {
	location.href = 'https://nimbleappgenie.live/dafri/auth/become-bank-agent';  
  }
  else if (page_name == "Affiliate Program") {
	location.href = 'https://nimbleappgenie.live/dafri/auth/affiliate-program';  
  }
  else if (page_name == "Dashboard") {
	location.href = 'https://nimbleappgenie.live/dafri/overview';  
  }
  else if (page_name == "Notifications") {
	location.href = 'https://nimbleappgenie.live/dafri/auth/notifications';  
  }
}

function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
          /*execute a function when someone clicks on the item value (DIV element):*/
              b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
			  //alert(inp.value);
			  goTo(inp.value)
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
          });
          a.appendChild(b);
        }
      }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
      x[i].parentNode.removeChild(x[i]);
    }
  }
}
/*execute a function when someone clicks in the document:*/
document.addEventListener("click", function (e) {
    closeAllLists(e.target);
});
}

autocomplete(document.getElementById("search_q"), countries);