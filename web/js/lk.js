function blockSidebarButtons(verify = true){
    let linkProfile = document.getElementById('sideBarProfileLink');
    let buttonProfile = document.getElementById('sideBarProfileBtn');

    let linkChannel = document.getElementById('sideBarChannelLink');
    let buttonChannel = document.getElementById('sideBarChannelBtn');

    let linkPayments = document.getElementById('sideBarPayLink');
    let buttonPayments = document.getElementById('sideBarPayBtn');

    let linkSub = document.getElementById('sideBarSubLink');
    let buttonSub = document.getElementById('sideBarSubBtn');

    let linkFinance = document.getElementById('sideBarFinLink');
    let buttonFinance = document.getElementById('sideBarFinBtn');

    let linkOptions = document.getElementById('sideBarOptionLink');
    let buttonOptions = document.getElementById('sideBarOptionBtn');

    if(verify){
        linkProfile.href = '#';
        buttonProfile.disabled = true;
    }
    else{
        linkProfile.href = '/lk/index';
        buttonProfile.disabled = false;
    }

    linkChannel.href = '#';
    buttonChannel.disabled = true;
    buttonChannel.classList.add('btn-lk-disabled');

    linkPayments.href = '#';
    buttonPayments.disabled = true;
    buttonPayments.classList.add('btn-lk-disabled');

    linkSub.href = '#';
    buttonSub.disabled = true;
    buttonSub.classList.add('btn-lk-disabled');

    linkFinance.href = '#';
    buttonFinance.disabled = true;
    buttonFinance.classList.add('btn-lk-disabled');

    linkOptions.href = '#';
    buttonOptions.disabled = true;
    buttonOptions.classList.add('btn-lk-disabled');
}