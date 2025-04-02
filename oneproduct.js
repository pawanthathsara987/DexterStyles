
        // Function to change the main product image when a thumbnail is clicked
        function changeImage(imagePath, thumbnailElement) {
            document.getElementById('mainProductImage').src = imagePath;
            
            // Remove active class from all thumbnails
            const thumbnails = document.querySelectorAll('.img-thumbnail');
            thumbnails.forEach(thumb =>thumb.classList.remove('thumbnail-active'));
            
            
            // Add active class to clicked thumbnail
            thumbnailElement.classList.add('thumbnail-active');
        }
        
        // Function to select size
        function selectSize(size, element) {
            // Check if size is unavailable
            if (element.classList.contains('size-unavailable')) return;
        
            // Update displayed selected size
            document.getElementById('selected-size').textContent = size;
            
            // Remove active class from all size boxes
            const sizeBoxes = document.querySelectorAll('.size-box');
            sizeBoxes.forEach(box =>box.classList.remove('size-active'));
            
            
            // Add active class to selected size box
            element.classList.add('size-active');
        }
        
        // Function to update subtotal based on quantity
        function updateSubtotal() {
            const basePrice = parseFloat(document.getElementById('product-base-price').value);
            const quantity = parseInt(document.getElementById('quantity').value);
            
            if (isNaN(quantity) || quantity < 1) {
                document.getElementById('quantity').value = 1;
                const subtotal = basePrice;
                document.getElementById('subtotal').textContent = 'Rs ' + subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            } else {
                const subtotal = basePrice * quantity;
                document.getElementById('subtotal').textContent = 'Rs ' + subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        }

        // function updateSubtotal() {
        //     const basePriceElement = document.getElementById('product-base-price');
        //     const basePrice = parseFloat(basePriceElement.textContent.replace(',', ''));
        
        //     const quantityInput = document.getElementById('quantity');
        //     let quantity = parseInt(quantityInput.value);
        
        //     if (isNaN(quantity) || quantity < 1) {
        //         quantity = 1;
        //         quantityInput.value = 1;
        //     }
        
        //     const subtotal = basePrice * quantity;
        //     document.getElementById('subtotal').textContent = 'Rs ' + subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        // }
        
        // Functions to handle quantity changes
        function incrementQuantity() {
            const quantityInput = document.getElementById('quantity');
            quantityInput.value = parseInt(quantityInput.value) + 1;
            updateSubtotal();
        }
        
        function decrementQuantity() {
            const quantityInput = document.getElementById('quantity');
            if (parseInt(quantityInput.value) > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
                updateSubtotal();
            }
        }

        function changeImage(imageSrc, thumbnailElement) {
            // Update main product image
            document.getElementById('main-product-image').src = imageSrc;
            
            // Remove active class from all thumbnails
            const thumbnails = document.querySelectorAll('.thumbnail-gallery .img-thumbnail');
            thumbnails.forEach(thumb => thumb.classList.remove('thumbnail-active'));
            
            // Add active class to clicked thumbnail
            thumbnailElement.classList.add('thumbnail-active');
        }
        
