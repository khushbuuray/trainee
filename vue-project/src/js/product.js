import axios from 'axios'; // we use for fetching data
import AddOrEditProduct from '../components/AddOrEdit.vue';

export default {
  name: 'ProductList',
  components: { AddOrEditProduct },
  data() {
    return {
      products: [],
      categories: [], // ← holds the unique categories
      filters: {
        title: '',
        category: ''
      },
      currentPage: 1,
      pageSize: 5,
      selectedProduct: {
        title: '',
        category: '',
        price: '',
        image: ''
      },
      isEditing: false
    };
  },
  mounted() { // Fetches data when the component is mounted.
    axios.get('https://fakestoreapi.com/products')
      .then(resp => {
        this.products = resp.data;

        // Extract unique categories
        const categorySet = new Set(resp.data.map(p => p.category));
        this.categories = [...categorySet]; // ... spread operator that user for seprating with the , 

      })
      .catch(err => {
        console.error('Error fetching data:', err);
      });
  },
  computed: {
    filteredProducts() {
      console.log(this.products);
      return this.products.filter(p => {
        return p.title.toLowerCase().includes(this.filters.title.toLowerCase()) &&
          (!this.filters.category || p.category === this.filters.category);
      });
    },
    paginatedProducts() {
      const start = (this.currentPage - 1) * this.pageSize;
      const end = start + this.pageSize;
      return this.filteredProducts.slice(start, end);
    },
    totalPages() {
      console.log(this.filteredProducts.length);
      return Math.ceil(this.filteredProducts.length / this.pageSize); // ← uses filtered list
      //Math.ceil i'm using to return new added array length
    },
    filteredPrice() {
      return this.paginatedProducts.reduce((sum, product) => {
        return sum + parseFloat(product.price)
      }, 0).toFixed(2);
    },
    totalPrice() {
      return this.products.reduce((sum, product) => {
        return sum + parseFloat(product.price)
      }, 0).toFixed(2);
    }
  },
  methods: {
    openAddModal() {
      this.isEditing = false;
      this.selectedProduct = { title: '', category: '', price: '', image: '' };
      this.$refs.productModal.open();
    },
    openEditModal(product) {
      console.log(product);
      this.isEditing = true;
      this.selectedProduct = { ...product };
      this.$refs.productModal.open();

    },
    handleSave(product) {
      console.log(product);
      console.log(this.isEditing);

      if (this.isEditing) {
        const index = this.products.findIndex(p => p.id === product.id);
        if (index !== -1) {
          this.products.splice(index, 1, product);
        }
      } else {
        const newId = this.products.length + 1;
        this.products.push({ ...product, id: newId });
        this.updateCategories();
      }
    },
    deleteProduct(productId) {
      if (confirm("Are you sure you want to delete this product?")) {
        this.products = this.products.filter(p => p.id !== productId);
        this.updateCategories();

      }
    },
    resetFilters() {
      this.filters.title = '';
      this.filters.category = '';
      this.currentPage = 1;
    },
    updateCategories() {
      const allCategories = this.products.map(p => p.category);
      this.categories = [...new Set(allCategories)]; // new set will take unique categories and ...  spread operator
    },
  },

  watch: {
    'filters.title'() {
      console.log('watch triggered');
      this.currentPage = 1;
    },
    'filters.category'() {
      this.currentPage = 1;
    }
  }
};
