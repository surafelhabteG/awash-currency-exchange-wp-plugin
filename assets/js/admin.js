function confirmation() {
    let action = document.forms["submit"]["actions"].value;

    if (action == "create") {
        if (confirm('are you sure want to create this records ?')) {
            return true;
        } else {
            return false;
        }
    }

    if (action == "delete") {
        if (confirm('are you sure want to delete those records ?')) {
            return true;
        } else {
            return false;
        }
    }

}

function calculate(choice = null) {
    const selectedButton = document.getElementById('selectedButton');
    if (choice !== null) {
        var isfrom = choice == 1 ? true : false;

        const selectedkeydown = document.getElementById('selectedkeydown');
        selectedkeydown.value = choice;

    } else {
        var isfrom = selectedkeydown.value == 1 ? true : false;
    }

    var hiddenValue = JSON.parse(document.getElementById(document.getElementById('fromDropdown').value).value);

    if (selectedButton.value == 1) {
        hiddenValue = hiddenValue.buying;

    } else {
        hiddenValue = hiddenValue.selling;
    }

    var textBoxValue = document.getElementById(isfrom ? 'fromValue' : 'toValue').value;

    document.getElementById(isfrom ? 'toValue' : 'fromValue').value =
        isfrom ? (+hiddenValue * +textBoxValue) : (+textBoxValue / +hiddenValue);

}

function clear(e) {
    document.getElementById('fromValue').value = "";
    document.getElementById('toValue').value = "";
}

function setSelectedButton(value = 1) {
    const selectedButton = document.getElementById('selectedButton');
    selectedButton.value = value;

    if (value == 1) {
        const buyingBtn = document.getElementById('buyingBtn');
        buyingBtn.style.backgroundColor = '#F88F33';
        buyingBtn.style.borderColor = '#CC6C29';
        buyingBtn.style.color = 'white';

        const sellingBtn = document.getElementById('sellingBtn');
        sellingBtn.style.backgroundColor = 'transparent';
        sellingBtn.style.borderColor = 'transparent';
        sellingBtn.style.color = 'gray';

        calculate(null);

    } else {
        const sellingBtn = document.getElementById('sellingBtn');
        sellingBtn.style.backgroundColor = '#F88F33';
        sellingBtn.style.borderColor = '#CC6C29';
        sellingBtn.style.color = 'white';

        const buyingBtn = document.getElementById('buyingBtn');
        buyingBtn.style.backgroundColor = 'transparent';
        buyingBtn.style.borderColor = 'transparent';
        buyingBtn.style.color = 'gray';

        calculate(null);

    }
}

function selectCurrencyDropdown(element) {
    var hiddenValue = JSON.parse(element.value);

    const currencyDropdownButton = document.getElementById('currencyDropdownButton');
    currencyDropdownButton.innerText = hiddenValue.currencyText.replaceAll('_', ' ');

    const fromDropdown = document.getElementById('fromDropdown');
    fromDropdown.value = hiddenValue.currency;

    const selectedCurrencyFlag = document.getElementById('selectedCurrencyFlag');
    const ImagebaseUrl = document.getElementById('ImagebaseUrl');

    selectedCurrencyFlag.src = ImagebaseUrl.value + hiddenValue.currency + '.png';

}