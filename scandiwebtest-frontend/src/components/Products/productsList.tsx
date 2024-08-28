import React from 'react';
import ProductCard from './productCard';

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

interface ProductListProps {
  products: Product[];
  selectedProducts: number[];
  onSelect: (productId: number) => void;
}

const ProductList: React.FC<ProductListProps> = ({ products, selectedProducts, onSelect }) => {

    return (
        <div className="container">
            <h1 className='text-white'>Product List</h1>
            <p className='text-white'>Product List</p>
            <span className='text-white'>Product List</span>
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
                        onSelect={onSelect}
                        />
                    ))
                )}
            </div>
        </div>
    );
};

export default ProductList;
