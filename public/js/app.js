window.addEventListener('load', (event) => {
    let anchors = document.getElementsByClassName('shot');
    for (let anchor of anchors) {
        anchor.addEventListener('click', open);
    }
});

let open = (event) => {
    const href = event.currentTarget.href;
    let xhr = new XMLHttpRequest();

    xhr.open('GET', href);
    xhr.responseType = 'text';

    xhr.onload = (e) => {
        const req = e.currentTarget;
        if (req.status === 200) {
            const response = JSON.parse(req.responseText);
            const modal = document.querySelector('.modal-div');
            if (response.url) {
                document.querySelector('.modal-img').src = response.url;
                document.querySelector('.modal-error').innerText = '';
            } else {
                document.querySelector('.modal-img').src = '';
                document.querySelector('.modal-error').innerText = response.errorMessage;
            }
            modal.classList.remove('hidden');
            modal.classList.add('show');
        } else if (req.status === 400) {
            const response = JSON.parse(req.responseText);
            document.querySelector('.modal-error').innerText = response.errorMessage;
            document.querySelector('.modal-img').src = '';
        }
    }
    xhr.send();
    event.preventDefault();
}

let closeModal = () => {
    const modal = document.querySelector('.modal-div');
    modal.classList.remove('show');
    modal.classList.add('hidden');
}