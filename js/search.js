document.getElementById('searchMethod').addEventListener('change', function() {
    if (this.value === 'mrn') {
        document.getElementById('mrnSearchForm').style.display = 'block';
        document.getElementById('dobSearchForm').style.display = 'none';
    } else {
        document.getElementById('mrnSearchForm').style.display = 'none';
        document.getElementById('dobSearchForm').style.display = 'block';
    }
});