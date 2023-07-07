window.onload = function () {
    let formInput = document.querySelector('.input-data-calculation');
    formInput.addEventListener('submit', async function (e) {
        e.preventDefault();
        params = new FormData(formInput);
        params.append('action', 'calculation');
        let request = await fetch('../controllers/ajax.php', {
            method: 'POST',
            body: params,
        });
        let response = await request.text();
        let table = document.querySelector('.table-payments');
        table.innerHTML = response;
    });
}