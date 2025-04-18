<template>
  <div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel"
    aria-hidden="true" ref="modal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form @submit.prevent="submitForm">
          <div class="modal-header">
            <h5 class="modal-title" id="createProductModalLabel">{{ isEdit ? 'Edit Product' : 'Create a New Product' }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="close"></button>
          </div>

          <div class="modal-body">
            <div class="form-group mb-3">
              <label for="title">Name</label>
              <input type="text" id="title" v-model="product.title" class="form-control" required />
            </div>
            
            <div class="form-group mb-3">
              <label for="category">Category</label>

              <!-- Show dropdown only when editing -->
              <select v-if="isEdit" id="category" v-model="product.category" class="form-control" required>
                <option disabled value="">Select Category</option>
                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
              </select>

              <!-- Show text input when creating -->
              <input v-else type="text" id="category" v-model="product.category" class="form-control" required />
            </div>

            <div class="form-group mb-3">
              <label for="price">Price</label>
              <input type="number" id="price" step="0.01"  min="0" v-model="product.price" class="form-control" required />
            </div>

            <div class="form-group mb-3">
              <label for="image">Image URL</label>
              <input type="file" id="image" @change="handleImageUpload" class="form-control" accept="image/*" />
              <div v-if="product.image" class="mt-2 text-center">
                <img :src="product.image" alt="Preview" style="max-width: 100px; max-height: 100px;" />
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="close">Cancel</button>
            <button type="submit" class="btn btn-primary">{{ isEdit ? 'Save Changes' : 'Add Product' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import add from '@/js/add';
export default add;
</script>


<style scoped>
.modal-dialog {
  max-width: 500px;
}
</style>
