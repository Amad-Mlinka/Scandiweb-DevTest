import React, { useState, useEffect } from 'react';
import axios from 'axios';
import ProductList from '../components/Products/productsList';
import Product from '../interfaces/Product';
import APIResponse from '../interfaces/Response';

const IndexPage: React.FC = () => {
  const [products, setProducts] = useState<Product[]>([]);  
  const [selectedProducts, setSelectedProducts] = useState<number[]>([]);

  useEffect(() => {
    axios.get<APIResponse>('https://amad.devdot.ba/requests/getProducts.php')
      .then(response => {
        try {
          const data = response.data.data;
          if (data) setProducts(data);
        } catch (error) {
          console.error('Error parsing response data:', error);
          setProducts([]);
        }
      })
      .catch(error => {
        console.error('There was an error fetching the products!', error);
      });
  }, []);

  const handleSelect = (productId: number) => {
    setSelectedProducts(prevState => {
      if (prevState.includes(productId)) {
        return prevState.filter(id => id !== productId);
      } else {
        return [...prevState, productId];
      }
    });
  };

  const handleMassHardDelete = () => {
    axios.post('https://amad.devdot.ba/requests/massHardDelete.php', {
      productIds: selectedProducts,
    })
    .then(response => {
      if (response.data.success) { 
        const remainingProducts = products.filter(product => !selectedProducts.includes(product.id));
        setProducts(remainingProducts);
        setSelectedProducts([]);
      } else {
        console.error('Delete failed:', response.data.message);
      }
    })
    .catch(error => {
      console.error('There was an error deleting the products!', error);
    });
  };

  return (
    <div>
       <h1 className='text-white'>Product List</h1>
            <p className='text-white'>Product List</p>
            <span className='text-white'>Product List</span>
      <button className="btn btn-outline-danger ml-2" onClick={handleMassHardDelete} id='delete-product-btn'>Mass Delete</button>
      <h1 className='text-white'>Product List</h1>
            <p className='text-white'>Product List</p>
            <span className='text-white'>Product List</span>
      <ProductList
        products={products}
        selectedProducts={selectedProducts}
        onSelect={handleSelect}
      />
    </div>
  );
};

export default IndexPage;
