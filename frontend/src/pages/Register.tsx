import { useState } from 'react';
import type { FormEvent } from 'react';
import { useAuth } from '../context/AuthContext';
import type { RegisterPayload } from '../types/auth';
import axios from 'axios';

export default function Register() {
  const { register } = useAuth();
  const [form, setForm] = useState<RegisterPayload>({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
  });
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError(null);
    try {
      await register(form);
    } catch (err) {
      if (axios.isAxiosError(err)) {
        setError(err.response?.data?.message ?? 'Hiba történt.');
      }
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <h1 className="text-white text-3xl">Helló hihi</h1>
      <input placeholder="Név" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} />
      <input placeholder="Email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} />
      <input type="password" placeholder="Jelszó" value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} />
      <input type="password" placeholder="Jelszó mégegyszer" value={form.password_confirmation} onChange={(e) => setForm({ ...form, password_confirmation: e.target.value })} />
      <input placeholder="Telefon" value={form.phone} onChange={(e) => setForm({ ...form, phone: e.target.value })} />
      <button type="submit">Regisztráció</button>
      {error && <p style={{ color: 'red' }}>{error}</p>}
    </form>
  );
}