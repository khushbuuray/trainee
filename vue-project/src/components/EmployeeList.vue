<template>
  <div class="page-wrapper">
    <h1 class="heading">Product List</h1>

    <div class="mb-3">
      <input type="text" placeholder="Filter by title" v-model="filters.title" />&nbsp;

      <!-- Filter by category -->
      <select v-model="filters.category">
        <option value="">All Categories</option>
        <option v-for="category in categories" :key="category" :value="category">
          {{ category }}
        </option>
      </select><br>
      <!-- Reset Button -->
      <button class="btn btn-secondary" @click="resetFilters">Reset</button>
    </div>


    <!-- Button to open modal -->
    <div style="padding-left:80%;">
      <button class="btn btn-success" @click="openAddModal">Create Product</button>
    </div>

    <!-- Reusable Modal Component -->
    <AddOrEditProduct ref="productModal" :product="selectedProduct" :isEdit="isEditing" :categories="categories"
      @save="handleSave" />


    <!-- Product Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover custom-table">
        <thead>
          <tr>
            <th>S.N</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Image</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(product, index) in paginatedProducts" :key="product.id">
            <td>{{ index + 1 }}</td>
            <td>{{ product.title }}</td>
            <td>{{ product.category }}</td>
            <td>{{ product.price }}</td>
            <td>
              <img :src="product.image" alt="product image" class="product-image" />
            </td>
            <td>
              <button class="btn btn-sm btn-primary me-2" @click="openEditModal(product)">Edit</button>
              <button class="btn btn-sm btn-danger" @click="deleteProduct(product.id)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="row">
        <h6>Total Product: {{ filteredProducts.length }}</h6>
        <h6 :style="{ paddingLeft: '500px' }">Total Price: {{ filteredPrice }}</h6>
        <h6 :style="{ paddingLeft: '1000px' }">Total Price Count:({{ totalPrice }})</h6>

      </div>

      <nav class="mt-3">
        <ul class="pagination justify-content-center">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button class="page-link" @click="currentPage">Previous</button>
          </li>

          <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: currentPage === page }">
            <button class="page-link" @click="currentPage = page">{{ page }}</button>
          </li>

          <li class="page-item" :class="{ disabled: currentPage === totalPages }">
            <button class="page-link" @click="currentPage">Next</button>
          </li>
        </ul>
      </nav>
    </div>

    <p v-if="!products.length" class="text-center">Loading...
    </p>
  </div>
</template>

<!-- <script>
import axios from 'axios'; // we use for fetching data
import AddOrEditProduct from './AddOrEdit.vue';

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
      return this.filteredProducts.reduce((sum, product) => {
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
      this.currentPage = 1;
    },
    'filters.category'() {
      this.currentPage = 1;
    }
  }
};
</script> -->

<script>
import product from '@/js/product';
export default product;
</script>