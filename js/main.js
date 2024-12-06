document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch('/api/payment_api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payment recorded successfully!');
            location.reload();
        } else {
            alert('An error occurred!');
        }
    })
    .catch(error => console.error('Error:', error));
});
