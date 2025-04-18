export default {
    props: {
      product: {
        type: Object,
        default: () => ({ title: '', category: '', price: '', image: '' })
      },
      isEdit: {
        type: Boolean,
        default: false
      },
      categories: {
        type: Array,
        default: () => []
      }
    },
    methods: {
      open() {
        // Use the ref to get the modal element and show it
        const modal = new bootstrap.Modal(this.$refs.modal);
        modal.show();
      },
      close() {
        // Use the ref to get the modal element and hide it
        const modal = bootstrap.Modal.getInstance(this.$refs.modal);
        modal.hide();
      },
      handleImageUpload(event) {
  
        const file = event.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = (e) => {
            this.product.image = e.target.result; // Base64 image data
          };  
          reader.readAsDataURL(file);
        }
      },
      submitForm() {
        console.log(this.product);
        // Emit the product data when form is submitted
        this.$emit('save', this.product);
        this.close(); // Close the modal after saving the product
      }
    }
  }