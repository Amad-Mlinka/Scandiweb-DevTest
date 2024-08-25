import React, { useState, useEffect } from 'react';
import axios from 'axios';
import ProductList from '../components/Products/productsList';

interface Product {
  id: number;
  SKU: string;
  name: string;
  price: number;
  active: number;
  type: string;
  weight?: string;
  size?: string;
  unit?: string;
  dimensions?: string;
}

const IndexPage: React.FC = () => {
  const [products, setProducts] = useState<Product[]>([]);  
  const [selectedProducts, setSelectedProducts] = useState<number[]>([]);

  useEffect(() => {
    axios.get('http://localhost/scandiweb-test/api/getProducts.php')
      .then(response => {
        try {
          const data = response.data;
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


  return (
    <div>
      <button className="btn btn-outline-danger ml-2" id='delete-product-btn'>Mass Delete</button>
      <ProductList
        products={products}
        selectedProducts={selectedProducts}
        onSelect={handleSelect}
      />
    </div>
  );
};

export default IndexPage;
