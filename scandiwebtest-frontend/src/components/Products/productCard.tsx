import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faCompactDisc, faCouch, faBook, faQuestionCircle } from '@fortawesome/free-solid-svg-icons';

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

interface ProductCardProps {
  product: Product;
  onSelect: (productId: number) => void;
}

const ProductCard: React.FC<ProductCardProps> = ({ product, onSelect }) => {
  const { SKU, name, price, size, unit, dimensions, weight, type } = product;

  const typeIcons: Record<string, any> = {
    '1': faCompactDisc,
    '2': faBook,
    '3': faCouch,
  };

  const icon = typeIcons[type] || faQuestionCircle;

  return (
    <div className="col-md-4 col-lg-3 mb-3">
      <div className="product-card card d-flex flex-column h-100 w-100 py-4">
        <div className="card-body">
          <input
            type="checkbox"
            className="delete-checkbox"
            onChange={() => onSelect(product.id)}
          />
          <h3 className="card-title text-white">
            {name} 
            <span className="badge bg-primary position-absolute top-0 end-0">
              <FontAwesomeIcon icon={icon} />
            </span>
          </h3>
          <p className="text-white">SKU: {SKU}</p>
          <p className="text-white">Price: ${price}</p>
          {size && <p className="text-white">Size: {size} {unit}</p>}
          {dimensions && <p className="text-white">Dimensions: {dimensions}</p>}
          {weight && <p className="text-white">Weight: {weight} KG</p>}
        </div>
      </div>
    </div>
  );
};

export default ProductCard;
