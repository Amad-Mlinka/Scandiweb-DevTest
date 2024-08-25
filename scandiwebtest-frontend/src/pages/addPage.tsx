import React, { useState, useEffect, useRef } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';
import APIResponse from '../interfaces/Response';
import { useNotification } from '../contexts/NotificationContext';

interface SpecificAttributes {
  [key: string]: string;
}

interface Errors {
  sku?: string;
  name?: string;
  price?: string;
  type?: string;
  [key: string]: string | undefined;
}

const AddProductPage: React.FC = () => {
  const [sku, setSku] = useState<string>('');
  const [name, setName] = useState<string>('');
  const [price, setPrice] = useState<string>('');
  const [type, setType] = useState<string>('');
  const [specificAttributes, setSpecificAttributes] = useState<SpecificAttributes>({});
  const [htmlContent, setHtmlContent] = useState<string>('');
  const { notifySuccess, notifyError } = useNotification();
  const [errors, setErrors] = useState<Errors>({});
  const formRef = useRef<HTMLDivElement | null>(null);
  const navigate = useNavigate();

  useEffect(() => {
    if (type) {
      axios.get<APIResponse>(`https://amad.totalh.net/api/requests/getTypeInput.php?typeId=${type}`)
        .then(response => {
          if (!response.data.success) {
            // Handle failure case
          } else {
            const html = response.data.data;
            setHtmlContent(html);
          }
        })
        .catch(error => {
          notifyError("There was an error fetching the type-specific inputs!");
          console.error('There was an error fetching the type-specific inputs!', error);
        });
    }
  }, [type]);

  useEffect(() => {
    if (formRef.current && htmlContent) {
      formRef.current.innerHTML = htmlContent;
      const inputs = formRef.current.querySelectorAll('input, select, textarea');
      inputs.forEach(input => {
        input.addEventListener('change', handleAttributeChange);
      });
      return () => {
        inputs.forEach(input => {
          input.removeEventListener('change', handleAttributeChange);
        });
      };
    }
  }, [htmlContent]);

  const handleTypeChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    setType(e.target.value);
    setSpecificAttributes({});
    setHtmlContent('');
    setErrors({});
  };

  const handleAttributeChange = (e: Event) => {
    const target = e.target as HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement;
    setSpecificAttributes(prev => ({
      ...prev,
      [target.name]: target.value,
    }));
    setErrors(prev => ({
      ...prev,
      [target.name]: '',
    }));
  };

  const validateInputs = (): boolean => {
    const newErrors: Errors = {};
  
    if (!sku) newErrors.sku = 'SKU is required';
    if (!/^[a-zA-Z0-9]+$/.test(sku)) newErrors.sku = 'SKU must be alphanumeric';
    
    if (!name) newErrors.name = 'Name is required';
    if (name.length > 50) newErrors.name = 'Name must be 50 characters or less';
    
    if (!price) newErrors.price = 'Price is required';
    if (price && !/^\d+(\.\d+)?$/.test(price)) newErrors.price = 'Price must be a valid number';
  
    if (!type) newErrors.type = 'Type is required';
  
    if (formRef.current) {
      const inputs = formRef.current.querySelectorAll('input, select, textarea');
      inputs.forEach(input => {
        const typedInput = input as HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement;
        const name = typedInput.name;
        const value = typedInput.value;
        const errorFeedback = input.nextElementSibling as HTMLDivElement;
  
        if (!value.trim()) {
          newErrors[name] = `${name.charAt(0).toUpperCase() + name.slice(1)} is required`;
          if (errorFeedback && errorFeedback.classList.contains('invalid-feedback')) {
            errorFeedback.classList.remove('d-none');
            errorFeedback.classList.add('d-block');
            errorFeedback.textContent = newErrors[name] || '';
          }
        } else if (!/^\d+(\.\d+)?$/.test(value)) {
          newErrors[name] = `${name.charAt(0).toUpperCase() + name.slice(1)} must be a valid number`;
          if (errorFeedback && errorFeedback.classList.contains('invalid-feedback')) {
            errorFeedback.classList.remove('d-none');
            errorFeedback.classList.add('d-block');
            errorFeedback.textContent = newErrors[name] || '';
          }
        } else if (errorFeedback && errorFeedback.classList.contains('invalid-feedback')) {
          errorFeedback.classList.add('d-none');
          errorFeedback.textContent = '';
        }
      });
    }
  
    setErrors(newErrors);
    if (Object.keys(newErrors).length !== 0) {
      notifyError("Please, submit required data");
    }
    return Object.keys(newErrors).length === 0;
  };
  

  const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    if (!validateInputs()) return;

    const attributes = Object.entries(specificAttributes);
    const formattedAttributes = formatAttributes();

    const newProduct = {
      sku,
      name,
      price,
      type,
      attributeName: attributes.length === 1 ? attributes[0][0] : 'dimensions',
      attributeValue: attributes.length === 1 ? attributes[0][1] : formattedAttributes,
    };

    axios.post<{ success: boolean; message?: string }>('https://amad.totalh.net/api/requests/addProduct.php', newProduct)
      .then(response => {
        if (response.data.success) {
          navigate('/');
        } else {
          notifyError(response.data.message);
        }
      })
      .catch(error => {
        notifyError('There was an error adding the product!');
        console.error('There was an error adding the product!', error);
      });
  };

  const formatAttributes = (): string | SpecificAttributes => {
    const attributes = Object.entries(specificAttributes);
    if (attributes.length === 3) {
      return attributes.map(([_, value]) => value).join('X');
    }
    return specificAttributes;
  };

  return (
    <div className="container">
      <h2 className='text-white'>Add New Product</h2>
      <form onSubmit={handleSubmit} noValidate className='d-flex flex-column gap-4' id="product_form">
        <div className="form-group">
          <label>SKU</label>
          <input
            type="text"
            className={`form-control form-control-lg ${errors.sku ? 'is-invalid' : ''}`}
            value={sku}
            onChange={(e) => setSku(e.target.value)}
            required
          />
          {errors.sku && <div className="invalid-feedback">{errors.sku}</div>}
        </div>
        <div className="form-group">
          <label>Name</label>
          <input
            type="text"
            className={`form-control form-control-lg ${errors.name ? 'is-invalid' : ''}`}
            value={name}
            onChange={(e) => setName(e.target.value)}
            required
          />
          {errors.name && <div className="invalid-feedback">{errors.name}</div>}
        </div>
        <div className="form-group">
          <label>Price</label>
          <input
            type="text"
            className={`form-control form-control-lg ${errors.price ? 'is-invalid' : ''}`}
            value={price}
            onChange={(e) => setPrice(e.target.value)}
            required
          />
          {errors.price && <div className="invalid-feedback">{errors.price}</div>}
        </div>
        <div className="form-group">
          <label>Type</label>
          <select
            className={`form-control form-control-lg ${errors.type ? 'is-invalid' : ''}`}
            value={type}
            onChange={handleTypeChange}
            required
          >
            <option value="">Select Type</option>
            <option value="1">DVD</option>
            <option value="2">Book</option>
            <option value="3">Furniture</option>
          </select>
          {errors.type && <div className="invalid-feedback">{errors.type}</div>}
        </div>

        <div ref={formRef}></div>

        <button type="submit" className="btn btn-primary">Add Product</button>
      </form>
    </div>
  );
};

export default AddProductPage;
