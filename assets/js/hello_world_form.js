const onLoad = () => {

    const inputUserName = document.getElementById('username');
    const inputDataList = document.getElementById('beername');

    if (inputDataList && inputUserName) {

        console.log("coucou");
        inputDataList.addEventListener('keyup', function (event) {

            if (inputDataList.value.length > 2) {
                // var sr = event.target.value;
                // if(!sr) return; //Do nothing for empty value
                //console.log(this.value);
                const data = new URLSearchParams();
                data.append('q', this.value);

                fetch('/search-beer', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: data
                })
                    .then((response) => response.json())
                    .then((data) => {
                        //console.log(data);
                        if (data) {
                            fillDataList(data);
                        }
                    });
            }
        });
    }
};

window.addEventListener('load', onLoad );

function validation(event, inputBeerName, inputUserName) {

    console.log('clicked submit !');

    var pattern = new RegExp('^[a-zA-Z0-9 ]+$');
    if (!pattern.test(inputUserName.value) || inputBeerName.value === '') {
        console.log('validations errors...');
        event.preventDefault();

        if (!pattern.test(inputUserName.value)) {
            console.log("bad user name !");
            let divErrorUser = document.createElement('div');
            divErrorUser.textContent = 'Please choose a user name !';
            divErrorUser.className = 'text-danger';
            inputUserName.before(divErrorUser);
        }

        if (inputBeerName.value === '') {
            let divErrorBeerName = document.createElement('div');
            divErrorBeerName.textContent = 'Please choose a beer name !';
            divErrorBeerName.className = 'text-danger';
            inputBeerName.before(divErrorBeerName);
        }
    } else {
        return true;
    }
}


function fillDataList(result) {
    const dataList = document.getElementById('beers_name_list');
    if(dataList) {
        if(dataList.children[0]) {
            dataList.children[0].remove();
        }
        result.forEach(item => {
            //console.log(item);
            console.log(item.name);
            if(!item)return;
            var option = document.createElement("option");
            option.text = item.name;
            option.value = item.name;
            dataList.appendChild(option);
        });
    }
}


function clearDropdown(){
    //dropdownMenu.replaceChildren();
    // dropdownMenu.innerHTML = '';
    // searchBoxContainer.classList.remove("is-loading");
}

//keep checking for an empty search box every 5 seconds
//     setInterval(function() {
//         if (!inputDataList.value) { //empty search box
//             clearDropdown()
//         }
//     }, 500);

// let form = document.getElementById("form");
// form.addEventListener("onkeyup", function(event) {
//     validation(event, inputBeerName, inputUserName);
// });

