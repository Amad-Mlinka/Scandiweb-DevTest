import React, { useEffect, useState } from 'react';
import ProductCard from './productCard';
import axios from 'axios';
import APIResponse from '../../interfaces/Response';

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


const ProductList: React.FC = () => {
    const [products, setProducts] = useState<Product[]>([]);  
    const [selectedProducts, setSelectedProducts] = useState<number[]>([]);
  

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

    return (
        <div className="container">
            <div className="d-flex flex-row justify-content-between mt-3">
                <h1 className="text-white">Product List</h1>
                <p className="text-white">Product List</p>
                <button className="btn btn-outline-danger ml-2" onClick={handleMassHardDelete} id='delete-product-btn'>Mass Delete</button>
            </div>

            <hr className='text-white'/>
            <div className="row">
                {products.length === 0 ? (
                    <div className="col-md-12 text-center">
                        <p>No Products Found</p>
                    </div>
                ) : (
                    products.map(product => (
                        <ProductCard
                        key={product.id}
                        product={product}
                        onSelect={handleSelect}
                        />
                    ))
                )}
            </div>
        </div>
    );
};

export default ProductList;
