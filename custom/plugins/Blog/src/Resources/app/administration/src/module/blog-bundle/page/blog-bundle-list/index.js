import template from './blog-bundle-list.html.twig';

const { Criteria } = Shopware.Data;

Shopware.Component.register('blog-bundle-list', {
  template,

  inject: [
      'repositoryFactory'  
    ],
    data() {
        return {
            bundles: [],
            repository: null,
             columns: [
        { property: 'name', label: 'Name' , routerLink: 'blog.bundle.detail'},
        { property: 'title', label: 'Title' }
        
      ]
        }
    }, 
    metaInfo() {
        return {
            title: this.$createTitle(),
        }
    },
    
    computed: {
        columns(){
            return this.getColumns();
        }
    },
    created() {
        this.createdComponent();

    },
    methods: {
        createdComponent(){
            this.repository = this.repositoryFactory.create('blog');
            this.repository.search(new Criteria(),Shopware.Context.api).then((result) => {
                this.bundles = result;
              console.log(result);

            });
        }
    },
    getColumns(){
        return [
            { property: 'name', label: this.$tc('blog-bundle-list.columnName') },
            { property: 'description', label: 'Description' },
        ]
    }
});