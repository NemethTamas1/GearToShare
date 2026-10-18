import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import api from '../../lib/axios';
import GearStatusRow from './GearStatusRow';

vi.mock('../../lib/axios');

const draftGear = {
    id: 5,
    title: 'Bosch fúró',
    status: 'draft',
} as any;

beforeEach(() => {
    vi.clearAllMocks();
});

describe('GearStatusRow', () => {
    it('draft eszköznél megjeleníti a Közzététel gombot', () => {
        // Arrange
        render(<GearStatusRow gear={draftGear} onStatusChanged={vi.fn()} />);

        // Act && Assert
        expect(screen.getByRole('button', { name: "Közzététel" })).toBeInTheDocument();
    });

    it('a megerősítés előtt nem hív API-t', async () => {
        // Arrange
        render(<GearStatusRow gear={draftGear} onStatusChanged={vi.fn()} />);

        // Act
        await userEvent.click(screen.getByRole('button', { name: 'Közzététel' }));

        // Assert
        expect(api.patch).not.toHaveBeenCalled();
    });

    it('a Mégse után eltűnik a megerősítő blokk', async () => {
        // Arrange
        render(<GearStatusRow gear={draftGear} onStatusChanged={vi.fn()} />)

        // Act
        await userEvent.click(screen.getByRole('button', { name: 'Közzététel' }));
        await userEvent.click(screen.getByRole('button', { name: 'Mégse' }));

        // Assert
        expect(screen.queryByText(/Biztos elérhetővé/)).not.toBeInTheDocument();
    });

    it('az Igen után egyszer hívja a PATCH-et a helyes adattal', async () => {
        // Arrange
        render(<GearStatusRow gear={draftGear} onStatusChanged={vi.fn()} />)
        vi.mocked(api.patch).mockResolvedValue({});

        // Act
        await userEvent.click(screen.getByRole('button', { name: 'Közzététel' }));
        await userEvent.click(screen.getByRole('button', { name: 'Igen, közzéteszem' }));

        // Assert
        expect(api.patch).toHaveBeenCalledTimes(1);
        expect(api.patch).toHaveBeenCalledWith('/api/gears/5/status', { status: 'available' });
    });

    it('az Igen után értesíti a szülőt', async () => {
        const onStatusChanged = vi.fn();
        vi.mocked(api.patch).mockResolvedValue({});
        render(<GearStatusRow gear={draftGear} onStatusChanged={onStatusChanged} />);

        await userEvent.click(screen.getByRole('button', { name: 'Közzététel' }));
        await userEvent.click(screen.getByRole('button', { name: 'Igen, közzéteszem' }));

        await waitFor(() => expect(onStatusChanged).toHaveBeenCalledTimes(1));
    });
});