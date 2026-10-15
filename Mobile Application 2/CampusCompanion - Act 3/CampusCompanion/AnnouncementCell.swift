import UIKit

final class AnnouncementCell: UITableViewCell {
    @IBOutlet private weak var categoryIconImageView: UIImageView!
    @IBOutlet private weak var titleLabel: UILabel!
    @IBOutlet private weak var dateLabel: UILabel!

    func configure(with announcement: CampusAnnouncement) {
        let isUrgent = announcement.priority == "Urgent"
        // Transparent UILabels must not be marked opaque: otherwise their
        // backing layers can render black where the text does not draw.
        titleLabel.isOpaque = false
        dateLabel.isOpaque = false
        titleLabel.backgroundColor = .clear
        dateLabel.backgroundColor = .clear
        titleLabel.textColor = .label
        titleLabel.text = announcement.title
        dateLabel.text = "\(announcement.category) • \(announcement.date)" + (isUrgent ? " • URGENT" : "")
        titleLabel.font = .preferredFont(forTextStyle: .headline)
        dateLabel.font = .preferredFont(forTextStyle: .subheadline)
        titleLabel.adjustsFontForContentSizeCategory = true
        dateLabel.adjustsFontForContentSizeCategory = true
        // Assign both branches on every configuration so reused urgent cells reset.
        categoryIconImageView.image = UIImage(systemName: isUrgent ? "exclamationmark.circle.fill" : "megaphone.fill")
        categoryIconImageView.tintColor = isUrgent ? .systemRed : .systemBlue
        dateLabel.textColor = isUrgent ? .systemRed : .secondaryLabel
        backgroundColor = isUrgent ? UIColor.systemRed.withAlphaComponent(0.07) : .systemBackground
        accessibilityLabel = "\(announcement.title), \(announcement.category), \(announcement.date), \(announcement.priority), posted by \(announcement.postedBy)"
        isAccessibilityElement = true
        accessibilityTraits = .button
    }
}
