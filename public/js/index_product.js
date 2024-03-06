document.addEventListener('DOMContentLoaded', function() {
    let categoryNames = document.querySelectorAll('.category-name');
    categoryNames.forEach(function(categoryName) {
        categoryName.addEventListener('click', function() {
            let productTable = this.nextElementSibling;
            if (productTable.style.display === 'table') {
                productTable.style.display = 'none';
            } else {
                productTable.style.display = 'table';
            }
        });
    });
});